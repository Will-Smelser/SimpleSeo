<?php
/**
 * Author: Will Smelser
 * Date: 12/22/13
 * Time: 5:20 PM
 * Project: simple-seo-api.com
 */

namespace api;

interface Itfidf{
    /**
     * Inverse Document Frequency
     * log base 2 of (total indexed pages in google, approx 11,000,000,000) / (total indexed pages with $word)
     * @return mixed
     */
    static function idf($word);

    /**
     * Calculate the Term Frequency * Inverse Document Frequency
     * @param $idf The Inverse Document Frequency
     * @param $frequency The number of occurences of word withing the document
     * @return mixed
     */
    static function tfidf($idf,$frequency);

    /**
     * Get the number of indexed pages for a given
     * word.
     * @param $word
     * @return mixed
     */
    static function getDocFrequency($word);
}

class TfIdf implements Itfidf {
    public static function idf($word){}
    public static function tfidf($idf,$frequency){}
    public static function getDocFrequency($word){}
} 