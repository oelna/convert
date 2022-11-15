<?php
	/*
	
	MIT License

	Copyright (c) 2022- Arno Richter
	https://arnorichter.de
	
	*/

	//check for PHP version requirements
	if(version_compare(phpversion(), '5.3.0', '<')) die('This requires at least PHP 5.3 to run.');
	date_default_timezone_set('Europe/Berlin');

	$conversions = array(
		array(
			'text' => 'UTF-8 Decode',
			'function' => function($input) {
				return utf8_decode($input);
			}
		),
		array(
			'text' => 'UTF-8 Encode',
			'function' => function($input) {
				return utf8_encode($input);
			}
		),
		array(
			'text' => 'Base64 Decode',
			'function' => function($input) {
				return base64_decode($input);
			}
		),
		array(
			'text' => 'Base64 Encode',
			'function' => function($input) {
				return base64_encode($input);
			}
		),
		array(
			'text' => 'JSON Decode',
			'function' => function($input) {
				return json_decode($input);
			}
		),
		array(
			'text' => 'JSON Encode',
			'function' => function($input) {
				return json_encode($input);
			}
		),
		array(
			'text' => 'URL Decode',
			'function' => function($input) {
				return rawurldecode($input);
			}
		),
		array(
			'text' => 'URL Encode',
			'function' => function($input) {
				return rawurlencode($input);
			}
		),
		array(
			'text' => 'Quoted-Printable Decode',
			'function' => function($input) {
				return quoted_printable_decode($input);
			}
		),
		array(
			'text' => 'Quoted-Printable Encode',
			'function' => function($input) {
				return quoted_printable_encode($input);
			}
		),
		array(
			'text' => 'HTMLEntities Decode',
			'function' => function($input) {
				return html_entity_decode($input);
			}
		),
		array(
			'text' => 'HTMLEntities Encode',
			'function' => function($input) {
				return htmlentities($input);
			}
		),
		array(
			'text' => 'HTMLSpecialchars Decode',
			'function' => function($input) {
				return htmlspecialchars_decode($input);
			}
		),
		array(
			'text' => 'HTMLSpecialchars Encode',
			'function' => function($input) {
				return htmlspecialchars($input);
			}
		),
		array(
			'text' => 'wpa_passphrase (ssid,pass)',
			'function' => function($input) {
				$input = preg_replace('#\s+#', ',', trim($input));
				list($ssid, $passphrase) = explode(',', $input, 2);

				$psk = hash_pbkdf2('sha1', trim($passphrase), trim($ssid), 4096, 32, true);
				return bin2hex($psk);
			}
		),
		array(
			'text' => 'Unix Timestamp to String',
			'function' => function($input) {
				return date('Y-m-d H:i:s', $input);
			}
		),
		array(
			'text' => 'String to UNIX Timestamp',
			'function' => function($input) {
				return strtotime($input);
			}
		),
		array(
			'text' => 'Detect Encoding',
			'function' => function($input) {
				return mb_detect_encoding($input, 'auto');
			}
		),
		array(
			'text' => 'Hex to RGB',
			'function' => function($input) {
				$input = ltrim(trim($input), '#');
				if(strlen($input) != 3 && strlen($input) != 6 ) return 'You need to enter either 6-digit or 3-digit hex colors';
				$input = (strlen($input) == 3) ? $input[0].$input[0].$input[1].$input[1].$input[2].$input[2] : $input;

				//thanks: http://stackoverflow.com/a/15202130/3625228
				list($r, $g, $b) = sscanf($input, "%02x%02x%02x");
				return $r.','.$g.','.$b.' <span style="display: inline-block; width: 1em; background: #'.$input.';">&nbsp;</span>';
			}
		),
		array(
			'text' => 'String length (UTF-8)',
			'function' => function($input) {
				return mb_strlen($input, 'UTF-8');
			}
		),
		array(
			'text' => 'Clean up white space',
			'function' => function($input) {
				return preg_replace('/\s+/S', ' ', $input);
			}
		),
		array(
			'text' => 'Clean up spaces, preserve newlines',
			'function' => function($input) {
				$input = preg_replace('/[ \t]+/', ' ', $input);
				return preg_replace('/[\r\n]+/', "\n", $input);
			}
		),
		array(
			'text' => 'Remove zero-width spaces',
			'function' => function($input) {
				$input = str_replace("&#8203;", "", $input); // remove html entities
				$input = str_replace("&#x200b;", "", $input);
				$input = preg_replace("/&[zZ]ero[wW]idth[sS]pace;/", "", $input);
				$input = str_replace("\xE2\x80\x8B", "", $input); // remove unencoded
				return $input;
			}
		),
		array(
			'text' => 'SHA1',
			'function' => function($input) {
				return hash('sha1', $input);
			}
		),
		array(
			'text' => 'SHA256',
			'function' => function($input) {
				return hash('sha256', $input);
			}
		),
		array(
			'text' => 'SHA512',
			'function' => function($input) {
				return hash('sha512', $input);
			}
		),
		array(
			'text' => 'MD5',
			'function' => function($input) {
				return hash('md5', $input);
			}
		),
		array(
			'text' => 'Random string generator',
			'function' => function($input) {
				$lower = base_convert(mt_rand().microtime(true)/2, 10, 36);
				$upper = strtoupper(base_convert(mt_rand().microtime(true)/2, 10, 36));
				return str_shuffle($lower.$upper)."\n\n".'123456789 123456789 123456789';
			}
		)
	);

	header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>Converter</title>

	<link rel="license" href="./LICENCE" />
	<meta name="author" content="Arno Richter" />
	<meta name="description" content="Convert between various string encodings (UTF-8, Base64, JSON or Quoted-Printable) and clean up text." />
	
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<style>
		* { box-sizing: border-box; }

		html {
			font: 100%/1.45 Helvetica, Arial, sans-serif;
		}

		#container {
			margin: 0 auto;
			width: 98%;
		}

		#output, form {
			margin-bottom: 2em;
			word-wrap: break-word;
		}

		textarea,
		#output pre {
			font-family: Consolas, Menlo, Andale Mono, monospace;
			white-space: pre-wrap;
		}

		a { color: black; }

		h1 {
			margin: 0;
		}

		select, input {
			font-size: 1.4em;
		}

		textarea {
			width: 100%;
			padding: 0.5em;
			min-height: 20em;
			font-size: 1em;
			resize: vertical;
			border: 0;
			background: #eee;
		}

		textarea:focus {
			outline: 1px solid #007aff;
		}

		#tosource, #toclipboard {
			color: #007aff;
			text-decoration: underline;
			display: inline-block;
			margin: 0 1em 1em 0;
			cursor: pointer;
		}

		#message {
			color: #4cd964;
			opacity: 0;
			transition: opacity 0.25s ease;
		}

		#message.show {
			opacity: 1;
		}

		@media all and (min-width: 60em) {
			#container {
				margin-top: 2em;
				width: 60%;
			}
		}

	</style>
</head>
<body>

	<div id="container">
		<form action="" method="post">
			<p>Host this on your own site or extend it: get <a href="https://gist.github.com/oelna/624b1f5376f0b3b9bff593fd1f9b1f24">the Gist</a></p>
			<h1>Input</h1>
			<textarea name="input" id="input" placeholder="Eingabe"><?php if(isset($_POST['input'])) echo($_POST['input']); ?></textarea>

			<select name="conversion">
				<?php foreach($conversions as $key => $conversion): ?>
				<option value="<?= $key ?>"<?php if(isset($_POST['conversion']) && $key == $_POST['conversion']) echo('selected') ?>><?= $conversion['text'] ?></option>
				<?php endforeach; ?>
			</select>
			<input type="submit" value="Convert" />
		</form>

		<div id="output">
			<h1>Output</h1>
			<a id="tosource">Copy to input field</a> <a id="toclipboard">Copy to clipboard</a> <span id="message">Copied!</span>
			<code><pre id="output-content"><?php
				if(isset($_POST['input']) && isset($_POST['conversion'])) {
					$result = call_user_func($conversions[$_POST['conversion']]['function'], $_POST['input']);
					echo($result);
				}
			?></pre></code>
		</div>
	</div>

	<script>
		var copyToSource = document.querySelector('#tosource');
		var copyToClipboard = document.querySelector('#toclipboard');
		var input = document.querySelector('#input');
		var output = document.querySelector('#output-content');
		var message = document.querySelector('#message');

		copyToSource.addEventListener('click', function(e) {
			e.preventDefault();

			var val = output.innerHTML;
			input.value = val;
		});

		copyToClipboard.addEventListener('click', function(e) {
			e.preventDefault();

			//as per http://stackoverflow.com/a/987376/3625228
			if(document.body.createTextRange) {
				range = document.body.createTextRange();
				range.moveToElementText(output);
				range.select();
			} else if(window.getSelection) {
				selection = window.getSelection();
				range = document.createRange();
				range.selectNodeContents(output);
				selection.removeAllRanges();
				selection.addRange(range);
			}

			try {
				if(!document.execCommand('copy')) {
					alert('Could not copy to clipboard');
				} else {
					message.classList.add('show');
					setTimeout(function() {
						message.classList.remove('show');
					}, 2500);
				}
			} catch(err) {
				alert('Could not copy to clipboard');
			}
		});

	</script>
</body>
</html>
