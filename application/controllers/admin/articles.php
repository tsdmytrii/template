<?php

class Articles extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    /*
     * Article seo functions
     */

    public function get_article_seo() {

        $article = new Article();

        $this->return_result($article->get_article_seo());

    }

    public function set_article_seo() {

        $article = new Article_language();

        $this->return_result($article->set_article_lang());

    }

    /*
     * Article item functions functions
     */

    public function get_article() {
        $article = new Article_item();

        $this->return_result($article->get_article_item());
    }

    public function set_article() {
        $article = new Article_item();

        $this->return_result($article->set_article_item());
    }

    public function get_all_article() {
        $article = new Article_item();

        $this->return_result($article->get_all_articles());
    }

    public function delete_article() {
        $article = new Article_item();

        $this->return_result($article->delete_article_item());
    }

}

?>
