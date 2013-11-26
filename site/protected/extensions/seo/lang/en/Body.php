<?php
namespace api\lang\en\Body;

use api\lang\Loader;


$zero = function($data){return Loader::$NONE;};


$loader = Loader::getLoader('Body','en');

$loader->register('checkH1',
        'Each page should have a unique H1 tag that describes the pages content and contains the target keyword.',
        'Good Job.  Just make sure you are not putting unimportant words in your H1 tag.',
        'You should only have one H1 tag per page and it should focus on key words for the page.',
        null,
        function($data){
            return (count($data) === 1)?Loader::$GOOD:Loader::$ERROR;
        }
);
$loader->register('checkH2',
        'H2 tags are considered to have a little more weight with search engines than other tags such as P, H3, H4, etc...You can however use these as frequently as necessary.',
        null,
        null,
        null,
        $zero
);
$loader->register('checkH3',
        'H3 tags do not cary any extra weight and should be used for design and page context.',
        null,
        null,
        null,
        $zero
);
$loader->register('checkH4',
        'H4 tags do not cary any extra weight and should be used for design and page context.',
        null,
        null,
        null,
        $zero
);
$loader->register('getKeyWords',
        'Key words shows you what your page is about.  Every page should have a focus and the key words used should reflect your search term goals.  Your targeted key words should also be in your TITLE tag, H1 tag, etc...',
        null,
        null,
        null,
        $zero
);
$loader->register('getPhrases',
    'This shows the context with which you are using key words.  Can help in identifying search phrases your page is targeting.',
    null,
    null,
    null,
    $zero
);
$loader->register('getTopPhrases',
    'This shows the context with which you are using key words.  Can help in identifying search phrases your page is targeting.',
    null,
    null,
    null,
    $zero
);
$loader->register('getPhrasesSpecific',
    'This shows the context with which you are using key words.  Can help in identifying search phrases your page is targeting.',
    null,
    null,
    null,
    $zero
);
$loader->register('checkInlineCSS',
        'Inline styles should be avoided when possible.  They add to the page size and do not take advantage of browser cache of linked style sheets.',
        null,
        'You should move these styles into a linked style sheet.',
        null,
        function($data){
                if(count($data) > 5) return Loader::$ERROR;
                if(count($data) <= 5 && count($data) > 0) return Loader::$WARN;
                return Loader::$GOOD;
            }
);
$loader->register('checkLinkTags',
    null,null,null,null,$zero
);
$loader->register('checkInlineStyle',
    null,null,null,null,$zero
);
$loader->register('checkForFrames',
    null,null,null,null,$zero
);
$loader->register('checkForIframes',
    null,null,null,null,$zero
);
$loader->register('checkForFlash',
    null,null,null,null,$zero
);
$loader->register('getInternalAnchor',
    null,null,null,null,$zero
);
$loader->register('getExternalAnchors',
    null,null,null,null,$zero
);
$loader->register('checkImages',
    null,null,null,null,$zero
);
