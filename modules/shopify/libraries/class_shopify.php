<?php
/**
* Copyright © 2017 Tomahawk. All rights reserved.
* Author: Jed Diaz
*/
CONST CUSTOM_COLLECTIONS  = '/admin/custom_collections.json';
CONST COLLECTION_PRODUCTS = '/admin/products.json?collection_id=';
CONST SINGLE_PRODUCT      = '/admin/products/';

class Shopify {

	private $params;
  private $action;

	public function __construct($params){
		$this->params = $params;
	}

	private function getApiUrl(){
		return $this->params->url;
	}

	private function getApiKey(){
		return $this->params->key;
	}

	private function getApiPassword(){
		return $this->params->password;
	}

  private function getAction(){
    return $this->action;
  }

  private function setAction( $endpoint ){
    $this->action = $endpoint;
  }

	private function send($params){

		$url          = $this->getAction();
		$headers      = [ "Content-type: text/xml;charset=\"utf-8\"",
                      "Accept: text/json",
                      "Cache-Control: no-cache",
                      "Pragma: no-cache"
                    ];
		$apiKey       = $this->getApiKey();
		$apiPassword  = $this->getApiPassword();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":" . $apiPassword);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    curl_close($ch);

    if($response){
      $json = json_decode($response, true);

      return $json;
    } 

        return '';
	}

	public function getCollections(){
        
       $this->setAction($this->getApiUrl().CUSTOM_COLLECTIONS);

       $response = $this->send($params);

       return $response;
	}

	public function getCollectionCatalog($collection_id){

		   $this->setAction($this->getApiUrl().COLLECTION_PRODUCTS.$collection_id);

       $response = $this->send($params);

       return $response;
	}

  public function getCollectionProduct($product_id){

       $this->setAction($this->getApiUrl().SINGLE_PRODUCT.$product_id.'.json');

       $response = $this->send($params);

       return $response;
  }

  public function getCollectionId($identifier){

    $collections = $this->getCollections();

    $collection_items = [];
    foreach($collections['custom_collections'] as $item){
      $collection_id  = $item['id'];
      $title          = $item['title'];
      $title_idnt     = str_replace('+','-',strtolower($title));
      $title_idnt     = str_replace('\'','-',$title_idnt);
      $title_idnt     = str_replace(' ','-',$title_idnt);

      $collection_title = ucfirst(strtolower($title));

      if($title_idnt == $identifier){

        $collections_selections = array(
                            'collection_id'    => $collection_id,
                            'collection_title' => $collection_title ,
                            );
      }
      
      array_push($collection_items, array('collection_id'    => $collection_id,
                                          'collection_url'   => $title_idnt,
                                          'collection_title' => $collection_title));
    }

    $collections = array(
                            'items'      => $collection_items,
                            'selections' => $collections_selections 
                            );

    return $collections;
  }
}