<?php



	define("FGETS_BUFFER_SIZE", 1024);

	function bxIsPhp5() {

		$aVersionInfo = explode(".", PHP_VERSION);
		$iMajorVersion = intval($aVersionInfo[0]);

		if($iMajorVersion>4) {
			return true;
		}
		else {
			return false;
		}

	}


	// based on http://de.php.net/http_build_query (mqchen at gmail dot com)
    function sxHttpBuildQuery($mParams, $sPrefix=null, $sSeparator="", $sKey="") {

        $sResult = array();

        foreach( (array) $mParams as $sKey => $jValue) {

            $sKey = urlencode($sKey);

            if(is_int($sKey) && $sPrefix != null) {
                $sKey = $sPrefix . $sKey;
            }

            if(!empty($sKeyey)) {
                $sKey = $sKeyey. "[" . $sKey . "]";
            }

            if(is_array($jValue) || is_object($jValue)) {
                array_push($sResult, sxHttpBuildQuery($jValue, "", $sSeparator, $sKey));
            }
            else {
                if(is_null($jValue)) {
	                array_push($sResult, $sKey . "=");
                }
                else {
	                array_push($sResult, $sKey . "=" . urlencode($jValue));
                }

            }
        }

        if(empty($sSeparator)) {
            $sSeparator = ini_get("arg_separator.output");
        }

        return implode($sSeparator, $sResult);
    }



	function sxEncodeParams($mParams) {

		if(!function_exists("http_build_query")) {
			$sEncodedParams = sxHttpBuildQuery($mParams);
		}
		else {
			$sEncodedParams = http_build_query($mParams);

			// Clean up the problem that NULL values do NOT get encoded by http_build_query
			$sSeparator = ini_get("arg_separator.output");

			foreach($mParams as $sKey=>$sValue) {
				if(is_null($sValue)) {
					$sEncodedParams .= $sSeparator . urlencode($sKey) . "=";
				}
			}

		}

		return $sEncodedParams;
	}


	function sxExtractHttpResponseBody($sHttpResponse) {

		$iMaxVal = strlen($sHttpResponse);

		$iLFPosition = strpos($sHttpResponse, "\n\n");

		if($iLFPosition===FALSE) {
			$iLFPosition = $iMaxVal;
		}

		$iCRLFPosition = strpos($sHttpResponse, "\r\n\r\n");

		if($iCRLFPosition===FALSE) {
			$iCRLFPosition = $iMaxVal;
		}

		$iBodyStartPosition = NULL;

		if($iCRLFPosition<$iLFPosition) {
			$iBodyStartPosition = $iCRLFPosition + 4;
		}
		else {
			$iBodyStartPosition = $iLFPosition + 2;
		}

		return substr($sHttpResponse, $iBodyStartPosition);

	}


	function __sxGetUrlContentPHPv4($sUrl, $mParams) {

		$mUrlParts = parse_url($sUrl);

		$sHost = $mUrlParts["host"];
		$iPort = $mUrlParts["port"];

		$sUser = $mUrlParts["user"];
		$sPassword = $mUrlParts["password"];

		$sPath = $mUrlParts["path"];
		$sParams = sxEncodeParams($mParams);

		if(empty($iPort)) {
			$iPort = 80;
		}

		$pFile = fsockopen($sHost, $iPort);

		fputs($pFile, "POST $sPath HTTP/1.0\n");
		fputs($pFile, "Host: $sHost\n");
		fputs($pFile, "Content-type: application/x-www-form-urlencoded\n");
		fputs($pFile, "Content-length: ". strlen($sParams) ."\n");
		fputs($pFile, "Connection: close\n");
		fputs($pFile, "\n");
		fputs($pFile, $sParams);

		$sResponse = "";

		while(!feof($pFile)) {
		      $sResponse .= fgets($pFile, FGETS_BUFFER_SIZE);
		}

		fclose($pFile);

		$sResponseBody = sxExtractHttpResponseBody($sResponse);

		return $sResponseBody;
	}


	function __sxGetUrlContentPHPv5($sUrl, $mParams) {

		$sEncodedParams = sxEncodeParams($mParams);
		$mStreamContextOptions = array("http" => array(	"method"  => "POST", "header"  => "Content-type: application/x-www-form-urlencoded\nContent-length: " . strlen($sEncodedParams), "content" => $sEncodedParams ));

		$pStreamContext = stream_context_create($mStreamContextOptions);
		$sResult = file_get_contents($sUrl, false, $pStreamContext);

		return $sResult;
	}


	function __sxGetUrlContent($sUrl, $mParams) {

		if(bxIsPhp5()) {
			return __sxGetUrlContentPHPv5($sUrl, $mParams);
		}
		else {
			return __sxGetUrlContentPHPv4($sUrl, $mParams);
		}

	}

	function __mxParseJson($sResponse) {

		if(function_exists("json_decode")) {

			return json_decode($sResponse, true);
		}
		else if (class_exists("Services_JSON")) {

			$pJson = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			return $pJson->decode($sResponse);
		}
		else {
			die("ERROR: No suitable JSON parser found.\n");
		}

	}

	function mxApiCall($sMethod, $mParams, $sDevKey=TAGSOLUTE_DEV_KEY) {

		$mParams["sDevKey"] = $sDevKey;
		$mParams["sMethod"] = $sMethod;

		// send request
		$sResponse = __sxGetUrlContent(TAGSOLUTE_API_URL, $mParams);

		$mResponse = __mxParseJson($sResponse);

		return $mResponse;
	}

?>