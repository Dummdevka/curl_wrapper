<?php

class CurlWrapper
{
    /**
     * Connection
     */
    protected $ch;

    /**
     * Options
     */
    protected $options;

    /**
     * Headers
     */
    protected $headers;

    /**
     * Useragent
     */
    protected $useragent;

    public function __construct( $useragent = [] ) {
        $this->useragent = $useragent ?? 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';

        $this->options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_USERAGENT => $this->useragent,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

    }

    public function makeRequest( string $url, string $method, array $data = [] ) {
        $this->ch = curl_init();

        $this->setMethod( $method );
        if( isset( $data['postData'] ) ){
            $this->setPostFields( $data['postData'] );
        }
        $this->setOptions( $url );

        isset( $data['headers'] ) ? $this->setHeaders( $data['headers'] ) : $this->setHeaders();
        isset( $data['options'] ) ? $this->setOptions( $url, $data['options'] ) : $this->setOptions( $url );
        isset( $data['cookie'] ) ?? $this->setCookies( $url, $data['cookie'] );

        $response = curl_exec( $this->ch );

        if( curl_errno( $this->ch) ){
            die( curl_error( $this->ch ) );
        }
        echo ' SUCCESS ' . PHP_EOL;
        
        return curl_getinfo( $this->ch, CURLINFO_HEADER_SIZE );
        //return 'SUCCESS!' . $response;
        //Handle errors
    }
    //Get type of request
    protected function setMethod( $method ) {
        switch ( $method ) {
            case 'POST': 
                curl_setopt( $this->ch, CURLOPT_POST, true);
                break;
            case 'PUT':        
                curl_setopt( $this->ch, CURLOPT_PUT, true);
                break;
            case 'DELETE':        
                curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case 'PATCH':        
                curl_setopt( $this->ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                break;
        }
    }

    //Set curl options
    protected function setOptions( string $url, array $options = [] ) : void {
        if( !empty( $options ) ){
            foreach( $options as $option => $value ){
                curl_setopt( $this->ch, constant($option), $value );
            }
        }
        curl_setopt( $this->ch, CURLOPT_URL, $url );
        curl_setopt_array( $this->ch, $this->options );
    }

    protected function setHeaders( array $headers = [] ) : void {
        if( !empty( $headers ) ){
            //This is an assoc array
            $this->headers = array_merge( $this->headers, $headers );
        } 

        $req_headers = [];
        foreach( $this->headers as $header => $value ){
            $str = $header . ':' . $value;
            $req_headers[] = $str;
        }

        curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $req_headers );
    }

    protected function setPostFields( array $postData ) : void {
        $postFields = json_encode( $postData );
        var_dump( $postFields );

        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $postFields );
    }

    protected function setCookieFile( $cookiefile ) {
        curl_setopt( $this->ch, CURLOPT_COOKIEJAR, $cookiefile ); 
    }

    protected function setCookie( array $cookie ) {
        foreach( $cookie as $cookie_str ){
            curl_setopt( $this->ch, CURLOPT_COOKIE, $cookie_str );
        } 
    }
    //Parse errors

}
