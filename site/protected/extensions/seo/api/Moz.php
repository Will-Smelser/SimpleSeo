<?php

namespace api;

require_once SEO_PATH_CLASS . 'MozConnect.php';
require_once SEO_PATH_ROOT . 'ApiKeys.php';

require_once(SEO_PATH_ROOT.'/../querypath/src/QueryPath.php');
require_once(SEO_PATH_ROOT.'/../querypath/src/qp.php');

class Moz implements ApiKeys{
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $url;
	
	/**
	 * @ignore
	 * @var unknown
	 */
	public $moz;

	/**
	 * @ignore
	 * @param unknown $url
	 */
	public function __construct($url){
		$this->url = $url;
		$this->moz = new \MozConnect(self::MOZ_USER,self::MOZ_PASS);
	}
	
	/**
	 * This is the moz link main page data
	 * @return multitype:NULL
	 */
	public function getMozLinks(){
		$html = $this->moz->getData(\MozServices::OSE, $this->url);

        $qp = htmlqp($html);
        $metrics = $qp->find(':root body .metrics .has-tooltip');

        //var_dump($metrics);
        $da = $metrics->get(0);
        $pa = $metrics->get(1);
        $ld = $metrics->get(3);
        $ti = $metrics->get(4);

		return array(
			'domainAuthority'=>trim(preg_replace('/\s+/',' ',$da->textContent)),
			'pageAuthority'=>trim(preg_replace('/\s+/',' ',$pa->textContent)),
			'linkingRootDomains'=>trim(preg_replace('/\s+/',' ',$ld->textContent)),
			'totalInboundLinks'=>trim(preg_replace('/\s+/',' ',$ti->textContent)),
		);
	}
	
	/**
	 * These are the SEOmoz just discovered data
	 * @return multitype:
	 */
	public function getMozJustDiscovered(){
		$html = $this->moz->getData(\MozServices::JD, $this->url);

        $results = array();

        $qp = htmlqp($html,'#results tr');
        $cnt = $qp->count();

        for($i=0;$i<$cnt;$i++){
            $tds = $qp->find('td');

            if($tds !== null && $tds->get(0) !== null){
                array_push($results, array(
                    'link'=>trim($tds->get(0)->textContent),
                    'text'=>trim($tds->get(1)->textContent),
                    'pageAuthority'=>trim($tds->get(2)->textContent),
                    'DomainAuthority'=>trim($tds->get(3)->textContent),
                    'DiscoveryTime'=>trim(preg_replace('/\s+/',' ',$tds->get(4)->textContent))
                ));
            }
            $qp = $qp->next();
        }
		return $results;
	}
}

?>
