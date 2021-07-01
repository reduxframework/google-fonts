<?php

/**
 * getSubsets Function.
 * Clean up the Google Webfonts subsets to be human readable
 *
 * @since ReduxFramework 0.2.0
 */
function getSubsets( $var ) {
	$result = array();

	foreach ( $var as $v ) {
		if ( strpos( $v, "-ext" ) ) {
			$name = ucfirst( str_replace( "-ext", " Extended", $v ) );
		} else {
			$name = ucfirst( $v );
		}

		array_push(
			$result, array(
			'id'   => $v,
			'name' => $name,
		)
		);
	}

	return array_filter( $result );
}  //function

/**
 * getVariants Function.
 * Clean up the Google Webfonts variants to be human readable
 *
 * @since ReduxFramework 0.2.0
 */
function getVariants( $var ) {
	$result = array();
	$italic = array();

	foreach ( $var as $v ) {
		$name = "";
		if ( $v[0] == 1 ) {
			$name = 'Thin 100';
		} elseif ( $v[0] == 2 ) {
			$name = 'Extra Light 200';
		} elseif ( $v[0] == 3 ) {
			$name = 'Light 300';
		} elseif ( $v[0] == 4 || $v[0] == "r" || $v[0] == "i" ) {
			$name = 'Regular 400';
		} elseif ( $v[0] == 5 ) {
			$name = 'Medium 500';
		} elseif ( $v[0] == 6 ) {
			$name = 'Semi-Bold 600';
		} elseif ( $v[0] == 7 ) {
			$name = 'Bold 700';
		} elseif ( $v[0] == 8 ) {
			$name = 'Extra Bold 800';
		} elseif ( $v[0] == 9 ) {
			$name = 'Black 900';
		}

		if ( $v == "regular" ) {
			$v = "400";
		}

		if ( strpos( $v, "italic" ) || $v == "italic" ) {
			$name .= " Italic";
			$name = trim( $name );
			if ( $v == "italic" ) {
				$v = "400italic";
			}
			$italic[] = array(
				'id'   => $v,
				'name' => $name,
			);
		} else {
			$result[] = array(
				'id'   => $v,
				'name' => $name,
			);
		}
	}

	foreach ( $italic as $item ) {
		$result[] = $item;
	}

	return array_filter( $result );
}   //function

date_default_timezone_set( 'UTC' );

$output = shell_exec( 'git log -1' );
echo shell_exec( 'git checkout -f master' );
$gFile             = dirname( __FILE__ ) . '/google_fonts.json';
$gFileminPHP       = dirname( __FILE__ ) . '/googlefonts.php';

$fonts             = array();

$arrContextOptions = array(
	'ssl' => array(
		'verify_peer'      => false,
		'verify_peer_name' => false,
	),
);

$key               = $argv[1];
$result            = json_decode( file_get_contents( "https://www.googleapis.com/webfonts/v1/webfonts?key={$key}", false, stream_context_create( $arrContextOptions ) ) );
$cd                = date( 'Y-m-d h:i:s:a' );

foreach ( $result->items as $font ) {
	$fonts[ $font->family ] = array(
		'variants' => getVariants( $font->variants ),
		'subsets'  => getSubsets( $font->subsets ),
	);
}

ksort( $fonts );
$data = json_encode( $fonts );

echo "Saving JSON File\n\n";
file_put_contents( $gFile, $data );

echo "Saving PHP\n\n";
$code = <<<PHP
<?php
// Last Updated : $cd
defined( 'ABSPATH' ) || exit; 
return json_decode( '$data', true );
PHP;
file_put_contents( $gFileminPHP, $code );
