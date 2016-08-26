<?php
/**
 * Created by PhpStorm.
 * User: Cshiwei
 * Date: 2016/8/26
 * Time: 13:54
 */

include 'TwitterQueryBuilder.php';

$twBuilder = new TwitterQueryBuilder();

//get exacat word
$word = "apple banana grape";
/*$twBuilder->setExactWord($word)
          ->buildQuery();
$query = $twBuilder->getQuery();*/
//you will get ?q="apple banana grape"

$twBuilder->setAllWord($word);
$query = $twBuilder->getQuery();
echo $query;


