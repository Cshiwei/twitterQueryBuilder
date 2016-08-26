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
 *
 */

$word = array(
  'apple','banana','grade'
);
$since = 1472195610;
$until = 1472195610;
$twBuilder->setOrWord($word)
          ->setSince($since)
          ->setUntil($until);

$register = $twBuilder->getType();

/**
 *  $register  Key words have been registered
 *  You can change it and pass it in as a parameter for builderQuery.
 *  array(3) {
 *       'orWord' =>
 *       string(24) "apple OR banana OR grade"
 *       'since' =>
 *      string(16) "since:2016-08-26"
 *       'until' =>
 *       string(16) "until:2016-08-26"
 *   }
 */

$register['orWord'] ='Peach OR plum';
//var_dump($register);
//$twBuilder->buildQuery('111111');
$b = array(
  1,2,3
);
$query = $twBuilder->getQuery($b);

echo $query;




