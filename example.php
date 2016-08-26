<?php
/**
 * Created by PhpStorm.
 * User: Cshiwei
 * Date: 2016/8/26
 * Time: 13:54
 */

include 'TwitterQueryBuilder.php';

$twBuilder = new TwitterQueryBuilder();

/**
 * contain exact word
 * you will get ?q="apple banana grape"
 */
$word = "apple banana grape";
$twBuilder->setExactWord($word)
          ->buildQuery();
$query = $twBuilder->getQuery();



/**
 * contain any word
 *  ?q=apple OR banana OR grade
 */
$query = $twBuilder->setOrWord($word)
                   ->getQuery();

$word =array(
    'apple','banana grade'
);
$query = $twBuilder->setOrWord($word)
                   ->getQuery();




/**
 * combination
 *?q=apple OR banana OR grade since:2016-08-26 until:2016-08-26
 */

$word = array(
  'apple','banana','grade'
);
$since = 1472195610;
$until = 1472195610;
$twBuilder->setOrWord($word)
          ->setSince($since)
          ->setUntil($until);

$query = $twBuilder->getQuery();


/**
 * mood Save by array
 * ?q=:) :( ?
 */
$otherWord = array(
  'active','negative','question'
);
$query = $twBuilder->setOtherWord($otherWord)
                   ->getQuery();

echo $query;



