<?php
/**
 * Created by PhpStorm.
 * User: Will
 * Date: 11/30/13
 * Time: 12:21 PM
 */

namespace api;

//require_once(SEO_PATH_ROOT.'/../querypath/src/QueryPath.php');
//require_once(SEO_PATH_ROOT.'/../querypath/src/qp.php');
//require_once(SEO_PATH_ROOT . '/../phpquery/phpquery.php');
require_once(SEO_PATH_CLASS . 'HtmlParser.php');


class HtmlParserAdapter {
    private $parser;
    private $host;
    private $url;

    public function __construct($url){
        $this->url = $url;

        $info = parse_url($url);
        $this->host = $info['host'];

        $html = file_get_contents($url);
        $this->parser = new \HtmlParser($html,$url);

        /*
        $qp = htmlqp($url);
        $this->parser = $qp;
        */

       // $this->parser = file_get_dom($url);

        /*


        $cb = function($el){
            //var_dump($el->attr('src'));
            var_dump($el->getAttribute('src'));
        };

        $doc = \phpQuery::newDocumentHTML($url);
        //var_dump($doc->find('body img')->text());
        $doc->find('body img')->each($cb);


        exit;
        */
    }

    public function findTags($tag){
        //$qp = $this->parser->find($tag);

        //if($qp === null)
            //return null;



        //var_dump($qp);
        //exit;

        //$return = array();

        /*
        $cnt = $qp->count();
        for($i=0;$i<$cnt;$i++){
            var_dump($qp->html());
            $node = new \Node($tag,$qp->html());
            $node->host = $this->host;
            array_push($return, $node);
            $qp = $qp->next();
        }
        return $return;
        $cb = function($index,$item){
            var_dump($index,$item->textContent);
        };
        //$qp->each($cb);
        */
        $result = array();
        foreach($this->parser->findTags($tag) as $img){
            $node = new \Node($tag,$img);
            $node->host = $this->host;
            array_push($result,$node);
        }
        return $result;
    }

    public function html(){
        return $this->parser->dom;
    }
} 