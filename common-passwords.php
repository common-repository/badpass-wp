<?php
/**
 * Tests whether the current logged in user is using a common password by running each of the common
 * passwords through the wp_check_password function along with the user's ID and password hash.
 */
function badpass_wp_test_password() {
	//An array of common passwords
	global $badpass_wp_common_passwords;
	
	//A user must be logged in before the test can run
	$using_common_password = false;
	if ( is_user_logged_in() ) {
		//Retrieve the logged in user details
		$current_user = wp_get_current_user();
		
		//Check each of the common passwords against the user details
		foreach ( $badpass_wp_common_passwords as $common_password ) {
			if ( wp_check_password( $common_password, $current_user->user_pass, $current_user->ID ) ) {
				$using_common_password = true;
				break;
			}
		}
		
		//Store the result in a user meta field
		if ( $using_common_password ) {
			update_user_meta( $current_user->ID, 'badpass_wp_using_common_password', 'true' );
		} else {
			update_user_meta( $current_user->ID, 'badpass_wp_using_common_password', 'false' );
		}
	}
	
	//Return the result
	return $using_common_password;
}

/**
 * An array of just over 500 common passwords that the plugin tests for.
 * 
 * @var array of strings
 */
$badpass_wp_common_passwords = array( '1111', '11111', '111111', '11111111', '112233', '1212', '121212',
						  '123123', '1234', '12345', '123456', '1234567', '12345678', '1313',
						  '131313', '2000', '2112', '2222', '232323', '3333', '4128', '4321',
						  '4444', '5150', '5555', '654321', '6666', '666666', '6969', '696969',
						  '7777', '777777', '7777777', '8675309', '987654', 'aaaa', 'aaaaaa',
						  'abc123', 'abgrtyu', 'access', 'access14', 'action', 'albert', 'alex',
						  'alexis', 'amanda', 'amateur', 'andrea', 'andrew', 'angel', 'angela',
						  'angels', 'animal', 'anthony', 'apollo', 'apple', 'apples', 'arsenal',
						  'arthur', 'asdf', 'asdfgh', 'ashley', 'asshole', 'august', 'austin',
						  'baby', 'badboy', 'bailey', 'banana', 'barney', 'baseball', 'batman',
						  'beach', 'bear', 'beaver', 'beavis', 'beer', 'bigcock', 'bigdaddy',
						  'bigdick', 'bigdog', 'bigtits', 'bill', 'billy', 'birdie', 'bitch',
						  'bitches', 'biteme', 'black', 'blazer', 'blonde', 'blondes', 'blowjob',
						  'blowme', 'blue', 'bond007', 'bonnie', 'booboo', 'boobs', 'booger',
						  'boomer', 'booty', 'boston', 'brandon', 'brandy', 'braves', 'brazil',
						  'brian', 'bronco', 'broncos', 'bubba', 'buddy', 'bulldog', 'buster',
						  'butter', 'butthead', 'calvin', 'camaro', 'cameron', 'canada', 'captain',
						  'carlos', 'carter', 'casper', 'charles', 'charlie', 'cheese', 'chelsea',
						  'chester', 'chevy', 'chicago', 'chicken', 'chris', 'cocacola', 'cock',
						  'coffee', 'college', 'compaq', 'computer', 'cookie', 'cool', 'cooper',
						  'corvette', 'cowboy', 'cowboys', 'cream', 'crystal', 'cumming', 'cumshot',
						  'cunt', 'dakota', 'dallas', 'daniel', 'danielle', 'dave', 'david',
						  'debbie', 'dennis', 'diablo', 'diamond', 'dick', 'dirty', 'doctor',
						  'doggie', 'dolphin', 'dolphins', 'donald', 'dragon', 'dreams', 'driver',
						  'eagle', 'eagle1', 'eagles', 'edward', 'einstein', 'enjoy', 'enter',
						  'eric', 'erotic', 'extreme', 'falcon', 'fender', 'ferrari', 'fire',
						  'firebird', 'fish', 'fishing', 'florida', 'flower', 'flyers', 'football',
						  'ford', 'forever', 'frank', 'fred', 'freddy', 'freedom', 'fuck', 'fucked',
						  'fucker', 'fucking', 'fuckme', 'fuckyou', 'gandalf', 'gateway', 'gators',
						  'gemini', 'george', 'giants', 'ginger', 'girl', 'girls', 'golden', 'golf',
						  'golfer', 'gordon', 'great', 'green', 'gregory', 'guitar', 'gunner',
						  'hammer', 'hannah', 'happy', 'hardcore', 'harley', 'heather', 'hello',
						  'helpme', 'hentai', 'hockey', 'hooters', 'horney', 'horny', 'hotdog',
						  'house', 'hunter', 'hunting', 'iceman', 'iloveyou', 'internet', 'iwantu',
						  'jack', 'jackie', 'jackson', 'jaguar', 'jake', 'james', 'japan', 'jasmine',
						  'jason', 'jasper', 'jennifer', 'jeremy', 'jessica', 'john', 'johnny',
						  'johnson', 'jordan', 'joseph', 'joshua', 'juice', 'junior', 'justin',
						  'kelly', 'kevin', 'killer', 'king', 'kitty', 'knight', 'ladies', 'lakers',
						  'lauren', 'leather', 'legend', 'letmein', 'little', 'london', 'love',
						  'lover', 'lovers', 'lucky', 'maddog', 'madison', 'maggie', 'magic',
						  'magnum', 'marine', 'mark', 'marlboro', 'martin', 'marvin', 'master',
						  'matrix', 'matt', 'matthew', 'maverick', 'maxwell', 'melissa', 'member',
						  'mercedes', 'merlin', 'michael', 'michelle', 'mickey', 'midnight', 'mike',
						  'miller', 'mine', 'mistress', 'money', 'monica', 'monkey', 'monster',
						  'morgan', 'mother', 'mountain', 'movie', 'muffin', 'murphy', 'music',
						  'mustang', 'naked', 'nascar', 'nathan', 'naughty', 'ncc1701', 'newyork',
						  'nicholas', 'nicole', 'nipple', 'nipples', 'oliver', 'orange', 'ou812',
						  'packers', 'panther', 'panties', 'paris', 'parker', 'pass', 'password',
						  'patrick', 'paul', 'peaches', 'peanut', 'penis', 'pepper', 'peter',
						  'phantom', 'phoenix', 'player', 'please', 'pookie', 'porn', 'porno',
						  'porsche', 'power', 'prince', 'princess', 'private', 'purple', 'pussies',
						  'pussy', 'qazwsx', 'qwert', 'qwerty', 'qwertyui', 'rabbit', 'rachel',
						  'racing', 'raiders', 'rainbow', 'ranger', 'rangers', 'rebecca', 'redskins',
						  'redsox', 'redwings', 'richard', 'robert', 'rock', 'rocket', 'rosebud',
						  'runner', 'rush2112', 'russia', 'samantha', 'sammy', 'samson', 'sandra',
						  'saturn', 'scooby', 'scooter', 'scorpio', 'scorpion', 'scott', 'secret',
						  'sexsex', 'sexy', 'shadow', 'shannon', 'shaved', 'shit', 'sierra',
						  'silver', 'skippy', 'slayer', 'slut', 'smith', 'smokey', 'snoopy',
						  'soccer', 'sophie', 'spanky', 'sparky', 'spider', 'squirt', 'srinivas',
						  'star', 'stars', 'startrek', 'starwars', 'steelers', 'steve', 'steven',
						  'sticky', 'stupid', 'success', 'suckit', 'summer', 'sunshine', 'super',
						  'superman', 'surfer', 'swimming', 'sydney', 'taylor', 'teens', 'tennis',
						  'teresa', 'test', 'tester', 'testing', 'theman', 'thomas', 'thunder',
						  'thx1138', 'tiffany', 'tiger', 'tigers', 'tigger', 'time', 'tits', 'tomcat',
						  'topgun', 'toyota', 'travis', 'trouble', 'trustno1', 'tucker', 'turtle',
						  'united', 'vagina', 'victor', 'victoria', 'video', 'viking', 'viper',
						  'voodoo', 'voyager', 'walter', 'warrior', 'welcome', 'whatever', 'white',
						  'william', 'willie', 'wilson', 'winner', 'winston', 'winter', 'wizard',
						  'wolf', 'women', 'xavier', 'xxxx', 'xxxxx', 'xxxxxx', 'xxxxxxxx', 'yamaha',
						  'yankee', 'yankees', 'yellow', 'young', 'zxcvbn', 'zxcvbnm', 'zzzzzz', '0',
						  '123456789', 'babygirl', 'lovely', 'rockyou' );
?>