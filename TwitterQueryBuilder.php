<?php
/**
 * Created by PhpStorm.
 * User: Cshiwei
 * Date: 2016/8/24
 * Time: 16:57
 * 本系统模仿twitter的高级搜索功能拼接查询字符串
 * The system to imitate the twitter search function of the advanced query string
 * 参考地址 https://twitter.com/search-advanced
 * Reference :  https://twitter.com/search-advanced
 */
class TwitterQueryBuilder
{
    /**
     * Final query statement
     * @var string
     */
    private $query ='';

    /**
     * Currently registered methods
     * @var array
     */
    private $type = array();

    /**Contains all the words, separated by spaces, each word in the article can not be close to
     * @var string
     */
    private $allWord ='';

    /**The exact phrase, the word as a whole, can not be separated
     * @var string
     */
    private $exactWord ='';

    /**Separated by spaces, the article contains any words can be
     * @var string
     */
    private $orWord ='';

    /**The space is not included in the article can not contain these words
     * @var string
     */
    private $outWord = '';

    /**Multiple topics are separated by spaces with or connections
     * @var string
     */
    private $topicWord = '';
    
    /**Starting time of the article
     * @var string
     */
    private $since ='';

    /**The termination time of the article
     * @var string
     */
    private $until ='';

    /**Language can only choose one
     * @var string
     */
    private $lang = '';

    /**From these accounts to separate the number of spaces to or connections
     * @var string
     */
    private $fromWord = '';

    /**Articles sent to these accounts
     * @var string
     */
    private $toWord = '';

    /**In the text mentioned these accounts with spaces to separate OR connection
     * @var string
     */
    private $mentionWord = '';

    /**Near the position
     * @var string
     */
    private $placeWord = '';

    /**Options under other columns
     * @var string
     */
    private $otherWord = '';


    /**The resetWord method is used to record the final results
     * @var string
     */
    private $resetRes = '';

    /**The array contains some corresponding relationships
     * @var array
     */
    private $config=array(
        'active'    =>':)',
        'negative'  =>':(',
        'question'  =>'?',
        'retweets'  =>'include:retweets',
    );

    /**Do you need to splice q=?
     * @var bool
     */
    private $needQ = true;

    /**
     * @param mixed $allWord
     * @return $this
     */
    public function setAllWord($allWord)
    {
        $allWord = $this->restWord('','',$allWord);
        $this->allWord = $allWord;
        $this->type['allWord'] = $allWord;
        return $this;
    }

    /**
     * @param  mixed $exactWord
     * @return $this
     */
    public function setExactWord($exactWord)
    {
        $exactWord = trim($exactWord);
        $this->exactWord = '"'.$exactWord.'"';
        $this->type['exactWord'] = $this->exactWord;
        return $this;
    }

    /**
     * @param mixed $orWord
     * @return $this
     */
    public function setOrWord($orWord)
    {
        $orWord = $this->restWord('','OR',$orWord);
        $this->orWord = $orWord;
        $this->type['orWord'] = $orWord;
        return $this;
    }

    /**
     * Exclude these words by spaces
     * @param string $outWord
     * @return $this
     */
    public function setOutWord($outWord)
    {
        $outWord = $this->restWord('-','',$outWord);
        $this->outWord = $outWord;
        $this->type['outWord'] = $outWord;
        return $this;
    }

    /**
     * @param string $topicWord
     * @return $this
     */
    public function setTopicWord($topicWord)
    {
        $res = $this->restWord('#','OR',$topicWord);
        $this->topicWord = $res;
        $this->type['topicWord'] = $res;
        return $this;
    }

    /**
     * @param string $since
     * @return $this
     */
    public function setSince($since)
    {
        $since = date('Y-m-d',$since);
        $this->since = "since:{$since}";
        $this->type['since'] = $this->since;
        return $this;
    }

    /**
     * @param string $until
     * @return $this
     */
    public function setUntil($until)
    {
        $until = date('Y-m-d',$until);
        $this->until = "until:{$until}";
        $this->type['until'] = $this->until;
        return $this;
    }

    /**
     * @param string $lang
     * @return $this
     */
    public function setLang($lang)
    {
        $lang = "lang:{$lang}";
        $this->lang = $lang;
        $this->type['lang'] = $lang;
        return $this;
    }

    /**
     * @param string $fromWord
     * @return $this
     */
    public function setFromWord($fromWord)
    {
        $fromWord = $this->restWord('from:','OR',$fromWord);
        $this->fromWord = $fromWord;
        $this->type['fromWord'] = $fromWord;
        return $this;
    }

    /**
     * @param string $toWord
     * @return $this
     */
    public function setToWord($toWord)
    {
        $toWord = $this->restWord('to:','OR',$toWord);
        $this->toWord = $toWord;
        $this->type['toWord'] = $toWord;
        return $this;
    }

    /**
     * @param string $mentionWord
     * @return $this
     */
    public function setMentionWord($mentionWord)
    {
        $mentionWord = $this->restWord('@','OR',$mentionWord);
        $this->mentionWord = $mentionWord;
        $this->type['mentionWord'] = $mentionWord;
        return $this;
    }

    /**
     * @param string $placeWord
     * @return $this
     */
    public function setPlaceWord($placeWord)
    {
        $placeWord='near"'.$placeWord.'"'.' within:15mi';
        $this->placeWord = $placeWord;
        $this->type['placeWord'] = $placeWord;
        return $this;
    }

    /**
     * @param array|string $otherWord
     * @return $this
     */
    public function setOtherWord(array $otherWord)
    {
        $item ='';
        foreach($otherWord as $key=>$val)
        {
            if(array_key_exists($val,$this->config))
            {
                $item .= ' '.$this->config[$val];
            }
        }
        $this->otherWord =trim($item);
        $this->type['otherWord'] = $this->otherWord;
        return $this;
    }

    /**
     * @param string $pre
     * @param string $link
     * @param $words
     * @return string
     */
    private function restWord($pre='',$link='',$words)
    {
        if(is_array($words))
        {
            $item = '';
            foreach($words as $key=>$val)
            {
                $val = trim($val);
                if($val)
                {
                    $item .=' '.$val;
                }
            }
            $this->restWord($pre,$link,$item);
        }
        else
        {
            $this->resetRes = '';
            $words = explode(' ',$words);
            foreach($words as $ke=>$va)
            {
                $va = trim($va);
                if($va)
                {
                    $this->resetRes .= ' '.$link.' '.$pre.$va;
                }
            }
            $this->resetRes = trim ($this->resetRes);
            $this->resetRes = trim($this->resetRes,$link);
            $this->resetRes = trim ($this->resetRes);
        }

        return $this->resetRes;
    }

    /**
     * reset all settings
     */
    public function init()
    {
        if(!empty($this->type))
        {
            foreach($this->type as $key=>$val)
            {
                $this->$key = '';
            }
        }

        $this->query = '';
        $this->type = array();
    }

    /**Can customize the registration array,
     * if an incoming register array is passed, the query is split by the parameter.
     * do not recommend this, should be in accordance with the official twitter format, otherwise there will be difficult to predict the error
     * @param $register
     * @return string
     */
    public function buildQuery(array $register=array())
    {
        $query = '';
        $register = !empty($register) ? $register : $this->type;

        foreach($register as $key=>$val)
        {
            $query .= ' '.$val;
        }

        $query =trim($query);

        if($this->needQ)
        $query = '?q='.$query;

        $this->init();
        return $query;
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return mixed
     * @internal param $item
     */
    public function getQuery()
    {
        if($this->query == '')
        {
            $query = $this->buildQuery(array());
        }

        return $query;
    }

    /**
     * @param boolean $needQ
     */
    public function setNeedQ($needQ)
    {
        $this->needQ = $needQ;
    }
}
