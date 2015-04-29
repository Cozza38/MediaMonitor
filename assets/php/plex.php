<?php

function getPlexToken()
{
	global $plexToken;
	global $plex_username;
	global $plex_password;

	if ( ! empty( $plex_username ) && ! empty( $plex_password ) )
	{

		$host    = "https://my.plexapp.com/users/sign_in.xml";
		$process = curl_init($host);
		curl_setopt($process, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/xml; charset=utf-8',
				'Content-Length: 0',
				'X-Plex-Client-Identifier: plexWatchWeb'
			));
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($process, CURLOPT_USERPWD, $plex_username . ":" . $plex_password);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_HTTPGET, true);
		curl_setopt($process, CURLOPT_POST, 1);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
		$data = curl_exec($process);

		//Check for 401 (authentication failure)
		$authCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
		if ( $authCode == 401 )
		{
			curl_close($process);
			$plexToken = '';
			$errorCode = "Plex.tv authentication failed. Check your Plex.tv username and password.";

			//Check for curl error
		}
		else
		{
			if ( curl_errno($process) )
			{
				$curlError = curl_error($process);
				echo $curlError;
				$errorCode = $curlError;
				curl_close($process);
				$plexToken = '';

			}
			else
			{
				$xml       = simplexml_load_string($data);
				$plexToken = $xml['authenticationToken'];
				if ( empty( $plexToken ) )
				{
					$errorCode = "Error: Could not parse Plex.tv XML to retrieve authentication code.";
					curl_close($process);
				}
				else
				{
					$errorCode = '';
					curl_close($process);

				}
			}
		}

	}
	echo( $errorCode );

	return ( $plexToken );
}