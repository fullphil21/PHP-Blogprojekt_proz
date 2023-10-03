<?php
#****************************************************************************************************#
				
				
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
			
				require_once('./include/config.inc.php');
				require_once('./include/form.inc.php');
				require_once('./include/db.inc.php');
				require_once('./include/dateTime.inc.php');


#****************************************************************************************************#

				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				$errorLogin = NULL;
				$filterID	= NULL;


#****************************************************************************************************#

				#**************************************#
				#********** CONTINUE SESSION **********#
				#**************************************#
				
				#********** PREPARE SESSION **********#
				session_name('wwwblogprojektde');
				
				
				#********** START/CONTINUE SESSION **********#
				session_start();
				
/*	
if(DEBUG_V)	echo "<pre class='debug auth value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_SESSION);					
if(DEBUG_V)	echo "</pre>";	
*/


				#*******************************************#
				#********** CHECK FOR VALID LOGIN **********#
				#*******************************************#
				
				if( isset($_SESSION['ID']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {

					
					#********** NO VALID LOGIN **********#
if(DEBUG)		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
					$loggedIn = false;
					
					// 1. Leere Session Datei löschen
					session_destroy();
					
					
				#********** VALID LOGIN **********#	
				} else {
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Login erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
					session_regenerate_id(true);
					
					$loggedIn = true;
				}



#****************************************************************************************************#


				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#


				#********** PREVIEW GET ARRAY **********#

/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_GET <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_GET);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#


				// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde
				if( isset($_GET['action']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
									
					// Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Parameter-Werte
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					$action = sanitizeString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$action: $action <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					
					// Schritt 3 URL: Je nach Parameterwert verzweigen
					
					#*********** LOGOUT **********#
					if( $action === 'logout' ) {
if(DEBUG)			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Logout wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\n";
										
						// Schritt 4 URL: Daten weiterverabeiten
					
						// 1. Session Datei löschen
						session_destroy();
					
						// 2. User auf öffentliche Index Seite umleiten
						header('LOCATION: index.php');
					
						// 3. Fallback
						exit();
					
					} // LOGOUT BRANCH ENDS HERE
					
					#*********** CATEGORY FILTER **********#

					if( $action === 'filterByCategory' ) {
if(DEBUG)			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Kategorie Filter wird gestartet... <i>(" . basename(__FILE__) . ")</i></p>\n";
							

						#*********** CAT ID **********#

						// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde
						if( isset($_GET['catID']) === true ) {
if(DEBUG)				echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'catID' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
														
							// Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Parameter-Werte
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
										
							$catID = sanitizeString($_GET['catID']);
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catID: $catID <i>(" . basename(__FILE__) . ")</i></p>\n";
							// Schritt 4 URL: Daten weiterverabeiten
						
							$filterID = $catID;
						
						} // CAT ID BRANCH ENDS HERE

					} // CATEGORY FILTER BRANCH ENDS HERE

				} // PROCESS URL PARAMETERS ENDS HERE
					
#****************************************************************************************************#

				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#
				
				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#


				// Schritt 1 FORM: Prüfen, ob das Formular abgeschickt wurde

				if( isset($_POST['formLogin']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'Login' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
				

					// Schritt 2 FORM: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$userEmailForm		= sanitizeString($_POST['f1']);
					$userPasswordForm = sanitizeString($_POST['f2']);

if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userEmailForm: $userEmailForm <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$userPasswordForm: $userPasswordForm <i>(" . basename(__FILE__) . ")</i></p>\n";

					//Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorUserEmailForm = validateEmail($userEmailForm);
					$errorUserPasswordForm = validateInputString($userPasswordForm, minLength: 4);


					#********** FINAL FORM VALIDATION(FIELDS VALIDATION) **********#
					if( $errorUserEmailForm !== NULL OR $errorUserPasswordForm !== NULL ) {
						//Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";	
						//Fehlermeldung an den User
						$errorLogin = 'Die Logindaten sind ungültig';

					} else {
						//Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";			
					
						// Schritt 4 Form: Daten weiterverabeiten
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Daten werden weiterverarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\n";

						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#


						// Schritt 1 DB: DB-Verbindung herstellen;
						$PDO = dbConnect(DB_NAME);

						#********** FETCH USER DATA FROM DATABASE BY EMAIL **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";


						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
						$sql = 'SELECT userID, userPassword FROM users
						WHERE userEmail = :userEmail';

						$params = array( 'userEmail' => $userEmailForm );


						// Schritt 3 DB: Prepared Statements
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($params);
							
						} catch(PDOException $error) {
if(DEBUG) 				echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}	
						
						// Schritt 4 DB: Datenbankoperationen auswerten und DB-Verbindung schließen

						$resultSet = $PDOStatement->fetch(PDO::FETCH_ASSOC);
/*
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$resultSet <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($resultSet);					
if(DEBUG_V)			echo "</pre>";
*/

						#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)		echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
						unset($PDO);


						#********** 1. VALIDATE EMAIL **********#
if(DEBUG)			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Validiere Email-Adresse... <i>(" . basename(__FILE__) . ")</i></p>\n";
						

						if( $resultSet === false ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '$userEmailForm' wurde nicht in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
	
							// NEUTRALE Fehlermeldung an den User
							$errorLogin = 'Diese Logindaten sind ungültig';
							
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '$userEmailForm' wurde in der DB gefunden. <i>(" . basename(__FILE__) . ")</i></p>\n";				
	
							#********** 2. VALIDATE PASSWORD **********#
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Validiere Passwort... <i>(" . basename(__FILE__) . ")</i></p>\n";


							if( password_verify($userPasswordForm, $resultSet['userPassword']) === false ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt NICHT mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
	
								// NEUTRALE Fehlermeldung an den User
								$errorLogin = 'Diese Logindaten sind ungültig';

							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt mit dem Passwort aus der DB überein. <i>(" . basename(__FILE__) . ")</i></p>\n";				
	

								#********** 3. PROCESS LOGIN **********#	
if(DEBUG)					echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Login wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>\n";

								#********** PREPARE SESSION **********#
								session_name('wwwblogprojektde');


								#********** START SESSION **********#
								if( session_start() === false ) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
		
									$errorLogin = 'Der Login ist nicht möglich! 
														Bitte aktivieren Sie in Ihrem Brwoser die Annahme von Cookies.';
		
								} else {
									// Erfolgsfall
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";

									#********** SAVE USER DATA INTO SESSION FILE **********#
									$_SESSION['ID']			= $resultSet['userID'];
									$_SESSION['IPAddress']	= $_SERVER['REMOTE_ADDR'];
/*
if(DEBUG_V)						echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)						print_r($_SESSION);					
if(DEBUG_V)						echo "</pre>";
*/

									#********** REDIRECT TO INTERNAL PAGE **********#
									header('LOCATION: dashboard.php');
										


								} // 3. PROCESS LOGIN ENDS HERE

							} // 2. VALIDATE PASSWORD ENDS HERE

						} // 1. VALIDATE EMAIL END HERE

					} // FINAL FORM VALIDATION(FIELDS VALIDATION) ENDS HERE

				} //PROCESS FORM LOGIN ENDS HERE


#****************************************************************************************************#


				#****************************************#
				#********** DB OPERATIONS ***************#
				#****************************************#

				// FETCH BLOG DATA FROM DB

				// Schritt 1 DB: Verbindung zur Datenbank aufbauen:
				$PDO = dbConnect(DB_NAME);
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Blog Daten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";


				// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
				$params 	= array();

				$sql = 'SELECT blogHeadline, blogImagePath, blogImageAlignment, blogContent, blogDate, catLabel, userFirstName, userLastName, userCity
						  FROM blogs INNER JOIN categories USING (catID) INNER JOIN users USING (userID)';

				if( $filterID !== NULL ) {
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Filtere Blog Beiträge nach Kategorie... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$sql.= ' WHERE catID = :catID';
					$params['catID'] = $filterID;
				}

				$sql.= ' ORDER BY blogDate DESC';


				// Schritt 3 DB: Prepared Statements
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					$PDOStatement->execute($params);
					
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				}

				// Schritt 4 DB: Daten weiterverarbeiten und DB-Verbindung schließen
				$blogData = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);

/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blogData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($blogData);					
if(DEBUG_V)	echo "</pre>";
*/

				// FETCH BLOG DATA FROM DB ENDS HERE		


				#****************************************************#

				
				// FETCH CATEGROIE DATA FROM DB

				// Schritt 1 DB: DB-Verbindung ist noch geöffnet
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Kategorie Daten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";

				// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
				$sql = 'SELECT catLabel, catID FROM categories';

				$params 	= array();

				// Schritt 3 DB: Prepared Statements
				try {
					// Prepare: SQL-Statement vorbereiten
					$PDOStatement = $PDO->prepare($sql);
					
					// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
					$PDOStatement->execute($params);
					
				} catch(PDOException $error) {
if(DEBUG) 		echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
					$dbError = 'Fehler beim Zugriff auf die Datenbank!';
				}

				// Schritt 4 DB: Daten weiterverarbeiten und DB-Verbindung schließen
				$categoriesArray = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);

/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoriesArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($categoriesArray);					
if(DEBUG_V)	echo "</pre>";
*/

				// Datenbank schließen
if(DEBUG_DB) echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($PDO);

				// FETCH CATEGORIE DATA FROM DB  ENDS HERE	

				#********** DB OPERATIONS ENDS HERE ***************#

#****************************************************************************************************#
?>

<!doctype html>

<html>
	
	<head>	
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>PHP Projekt-Blog</title>
		
		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
		<link rel="stylesheet" href="./css/pageElements.css">

		<style>
			main {
				width: 60%;
			}
			aside {
				width: 30%;
				overflow: hidden;
			}
		</style>

	</head>
	
	<body>		
		<!-- -------- PAGE HEADER START -------- -->
		<br>
		<header class="fright loginheader">
		
			<!-- -------- LOGIN FORM START -------- -->
			<?php if( $loggedIn === false): ?>
			<form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="POST">
				<input type="hidden" name="formLogin">
				<fieldset>
					<legend>Login</legend>					
					<span class='error'><?= $errorLogin ?></span><br>
					<input class="short" type="text" name="f1" placeholder="Email-Adresse...">
					<input class="short" type="password" name="f2" placeholder="Passwort...">
					<input class="short" type="submit" value="Login">
				</fieldset>
			</form>
			<?php endif ?>
			<!-- -------- LOGIN FORM END -------- -->		
			
			<?php if( $loggedIn ): ?>
				<p><a href="dashboard.php">zum Dashboard >></a></p>
				<p><a href="?action=logout"><< Logout</a></p>
			<?php endif ?>
		</header>
		<div class="clearer"></div>
		
		<hr>
		<!-- -------- PAGE HEADER END -------- -->
		<h1>PHP Projekt-Blog</h1>
		<p><a href="index.php">Alle Einträge anzeigen</a></p>
		
		<main class="fleft">
		
			
			<!-- -------- USER MESSAGES START -------- -->

			<!-- -------- USER MESSAGES END -------- -->



			<!-- -------- BLOG START --------- -->
			
			<div class="blogs">
				<?php foreach ($blogData as $blog): ?>
					<div class="blog"> 
						<span class="fright blogCategory">Kategorie: <?= $blog['catLabel'] ?></span><br>
						<h2 class="blogHeadline"><?= $blog['blogHeadline'] ?></h2>
						<p class="blogUserInfo"><?= $blog['userFirstName'] . ' ' . $blog['userLastName'] . 
								'(' . $blog['userCity'] . ')  schrieb am ' . isoToEuDateTime($blog['blogDate'])['date'] . 
								' um ' . isoToEuDateTime($blog['blogDate'])['time'] . ' Uhr:' ?> </p>
				
						<div class="blogContent">
							<p>
								<?php if ($blog['blogImagePath'] !== NULL): ?>
									<img class="blogPicture" style="float:<?= $blog['blogImageAlignment'] ?>" src="<?= $blog['blogImagePath']?> " />
								<?php endif ?>
								
							<?= nl2br($blog['blogContent']) ?>
						
						</p>
						</div>
						<div class="clearer"></div>
					</div>
				<?php endforeach ?>
			</div>
			
		
			
			<!-- -------- BLOG END --------- -->
		</main>
		
		<aside class="fright">
		
		<div class="categories">

		<?php foreach ($categoriesArray as $category): ?>
			<p class="catLabels"><a class="catLabel" href="?action=filterByCategory&catID=<?=$category['catID']?>"><?= $category['catLabel']?></a></p>	
		<?php endforeach ?>

		</div>
		
			
		</aside>
		
		<div class="clearer"></div>
		
		
		
		

		

		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		
	</body>
	
</html>