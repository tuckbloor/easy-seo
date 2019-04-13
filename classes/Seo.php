<?php

    set_time_limit(60);

    class Seo {

        private $url;
        private $speed;
        private $ch;
        private $user_agent;
        private $tags = ['title', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
        private $tags_text_array = [];
        private $data;
        private $meta_tags;
        private $word_count;

        public function __construct($url)

        {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                exit($url . ' Is Not A Valid URL');
            }

            $this->url = rtrim($url, '/'); //remove trailing slash
            $this->user_agent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)'; // set the user agent
        }

        /**
         * @return bool
         */
        public function run()

        {

            header('Content-Type: application/json;charset=utf-8');

            $this->ch = curl_init();

            curl_setopt($this->ch, CURLOPT_URL, $this->url); //crawl the url
            curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent); //the user agent we set above
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it directly.
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 2); //The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
            curl_setopt($this->ch, CURLOPT_TIMEOUT, 30); //The maximum number of seconds to allow cURL functions to execute.
            //curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

            $this->data = curl_exec($this->ch);

            //check for curl error
            if (curl_errno($this->ch)) {

                //we have an error exit
                exit('Curl error: ' . curl_error($this->ch));

            }

            //get the curl information
            $info = curl_getinfo($this->ch);

            //Close the CURL session and frees all resources.
            curl_close($this->ch);

            //get the total time taken for the curl script to run
            $this->speed = ['time' => $info['total_time']];

            return json_encode($this->speed);

        }


        /**
         * @return false|string
         */
        public function getTags()

        {

            header('Content-Type: application/json;charset=utf-8');

            //loop through all the tags we want text from
            foreach ($this->tags as $key => $tag) {

                //get the text between the tags
                $text = $this->getTagText($this->data, $tag);

                //put the tag in the returned array
                $this->tags_text_array[$tag] = $text;

                //put the qty of this tags in the returned array
                $this->tags_text_array[$tag]['numberOfTags'] = sizeof($text);

                //count number of characters
                if($tag == 'title') {
                    $this->tags_text_array[$tag]['titleLength'] = strlen($text[0]);
                }

            }

            return json_encode($this->tags_text_array);

        }

        /**
         * @param $string
         * @param $tag
         * @return mixed
         */
        function getTagText($string, $tag)

        {

            //get the text between each tag
            $pattern = "#<\s*?$tag\b[^>]*>(.*?)</$tag\b[^>]*>#s";
            preg_match_all($pattern, $string, $matches);

            return $matches[1];

        }

        /**
         * @return false|string
         */
        public function getMetaTags()

        {
            header('Content-Type: application/json;charset=utf-8');

            //get the meta tags (only works if name is associated with the meta tag)
            $this->meta_tags= get_meta_tags($this->url);


            return json_encode($this->meta_tags);
        }



        /**
         * @return false|string
         */
        public function getWordCount()

        {

            header('Content-Type: application/json;charset=utf-8');

            //strip all tags except the body tag
            $body = strip_tags($this->data, '<body>');

            //just get the text in the body tag
            $words = $this->getTagText($body, 'body');

            //$words is an array
            $word_count = str_word_count($words[0]);

            //add the word count to the array to be returned
            $this->word_count = $word_count;

            //return json encoded array
            return json_encode($this->word_count);
        }
    }