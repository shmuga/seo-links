<?php
include_once 'Domain.class.php';

class PageDownloader{
    private $_userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.94 Safari/537.36';


    private $_keywords;

    public function __construct($keywords) {
        $this->_keywords = $keywords;
    }

    public function getSites($page = 0){
        $ch = curl_init ();
        // echo '<pre>',var_dump("http://www.google.com/search?q=" . rawurlencode($this->_keywords) . "&start=" . $page *10),'</pre>';
        $url = "http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=".rawurlencode($this->_keywords)."&start=" . $page * 4;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_USERAGENT, $this->_userAgent); // set user agent
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $output = curl_exec ($ch);
        curl_close($ch);
        return $output;
    }
    
    public function getIndexOfSite($site){
        $ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL, $site);
        curl_setopt ($ch, CURLOPT_USERAGENT, $this->_userAgent); // set user agent
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $output = curl_exec ($ch);
        curl_close($ch);
        return $output;   
    }

    public function checkLink($link){
        $ch = curl_init($link);     
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);            
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        $data = curl_exec($ch);
        curl_close($ch);
        preg_match_all("/HTTP\/1\.[1|0]\s(\d{3})/",$data,$matches);
     
        $code = end($matches[1]);
     
        if(!$data) 
        {
            return(true);
        } 
        else 
        {
            if($code==200) 
            {
                return(false);
            } 
            elseif($code==404) 
            {
                return(true);
            }
            
        }
            
    }    

    public function checkDomain($domain){
        $ch = curl_init ();
        $url = "http://freedomainapi.com/?key=u2xuei9pk0&domain=" . $domain;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_USERAGENT, $this->_userAgent); // set user agent
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $output = curl_exec ($ch);
        curl_close($ch);
        return json_decode($output);
    }

}


class PageParser{
    public function parseGoogle($page){
        $matches = array();
        $page = json_decode($page);
        foreach ($page->responseData->results as $key => $value) {
            $matches[] = $value->url;
        }
        return $matches;
    }

    public function parsePageForLinks($page){
        $matches = '';
        preg_match_all('/href="https?:\/\/(.*?)"/', $page, $matches);         
        return $matches[1];              
    }

    public function getDomain($link){
        try {
            $domain = Domain::from_url($link);
            return $domain->get_reg_domain();            
        } catch (Exception $e) {
            return false;               
        }
    }
}
 
// $pd = new PageDownloader('free sites');
?>