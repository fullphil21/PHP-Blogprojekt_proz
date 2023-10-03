<?php
#****************************************************************************************************#
				
				
				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
				
				require_once('./include/config.inc.php');
				require_once('./include/form.inc.php');
				require_once('./include/db.inc.php');



#****************************************************************************************************#


				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#

				$userFirstName 				= NULL;
				$userLastName 					= NULL;

				$newCatLabelForm				= NULL;
				
				$errorNewCatLabelForm 		= NULL;
				$successNewCatLabelForm 	= NULL;

				$errorHeadlineForm			= NULL;
				$errorContentForm				= NULL;

				$catIDForm		 				= NULL;
				$headlineForm					= NULL;
				$contentForm					= NULL;
				$alignPicForm					= NULL;

				$blogImagePathForm			= NULL;
				$errorImageUpload				= NULL;

#****************************************************************************************************#

				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#
				
				#********** PREPARE SESSION **********#
				session_name('wwwblogprojektde');

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
					// Fehlerfall
if(DEBUG)		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
					#********** DENY PAGE ACCESS **********#
					// 1. Leere Session Datei löschen
					session_destroy();

					// 2. User auf öffentliche Index Seite umleiten
					header('LOCATION: index.php');

					// 3. Fallback
					exit();

				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Login erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";				

					session_regenerate_id(true);
										
					$userID = $_SESSION['ID'];

				}
				// CHECK FOR VALID LOGIN ENDS HERE


#****************************************************************************************************#

				#**************************************************#
				#********** FETCH USER DATA FROM DB ***************#
				#**************************************************#


				// Schritt 1 DB: Verbindung zur Datenbank aufbauen:
				$PDO = dbConnect(DB_NAME);
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";


				// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
				$sql = 'SELECT userFirstName, userLastName
						  FROM users
						  WHERE userID = :userID';

				$params 	= array( 'userID' => $userID );

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
				$userData = $PDOStatement->fetch(PDO::FETCH_ASSOC);

/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$userData <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($userData);					
if(DEBUG_V)	echo "</pre>";
*/

				// Datenbank schließen
if(DEBUG_DB) echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($PDO);

				// Werte aus $userData in Variablen umkopieren

				$userFirstName = $userData['userFirstName'];
				$userLastName = $userData['userLastName'];

				// FETCH USER DATA FROM DB ENDS HERE				


#****************************************************************************************************#

				#*******************************************************#
				#********** PROCESS URL PARAMETERS FOR LOGOUT **********#
				#*******************************************************#


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

				} // PROCESS URL PARAMETERS FOR LOGOUT ENDS HERE



#****************************************************************************************************#


				#********************************************************#
				#********** PROCESS FORM CREATE CATLABEL DATA **********#
				#********************************************************#
				
				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#

				// Schritt 1 FORM: Prüfen, ob das Formular abgesendet wurde
				if( isset($_POST['formCreateCatLabelData']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Das Formular 'Create CatLabel Data' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
									
					// Schritt 2 FORM: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Die Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$newCatLabelForm = sanitizeString( $_POST['f1'] );
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$newCatLabelForm: $newCatLabelForm <i>(" . basename(__FILE__) . ")</i></p>\n";

					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>:Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";

					$errorNewCatLabelForm = validateInputString($newCatLabelForm);


					#********** FINAL FORM VALIDATION (FIELDS VALIDATION) **********#
					if( $errorNewCatLabelForm !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						
						// Schritt 4 FORM: Daten weiterverarbeiten
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Daten werden weiterverarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#

						// Schritt 1 DB: Verbindung zur Datenbank aufbauen
						$PDO = dbConnect(DB_NAME);

						#********** 1. CHECK IF CATEGORIE ALREADY EXITS **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe, ob Kategorie bereits in der DB vorhanden ist... <i>(" . basename(__FILE__) . ")</i></p>\n";			


						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
						$sql 		= 'SELECT COUNT(catLabel) FROM categories
										WHERE catLabel = :catLabel';
							
						$params 	= array( 'catLabel' => $newCatLabelForm );
				
				
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
				
						// Schritt 4 DB: Daten weiterverarbeiten und DB-Verbindung schließen

						$count = $PDOStatement->fetchColumn();

						if( $count !== 0 ) {
							//Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Kategorie '$newCatLabelForm' ist bereits in der DB registriert! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							$errorNewCatLabelForm = 'Es existiert bereits eine Kategorie mit diesem Namen!';

						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Kategorie '$newCatLabelForm' ist noch nicht in der DB registriert! <i>(" . basename(__FILE__) . ")</i></p>\n";
						
							#********** 2. SAVE CATLABEL DATA INTO DB **********#

							// Schritt 1 DB: -> Datenbank ist noch offen

							// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
							$sql = 'INSERT INTO categories
									  (catLabel)
									  VALUES
									  (:catLabel)';

							$params = array( 'catLabel' => $newCatLabelForm );

							//Schritt 3 DB: Prepared Statements

							try {
								// Prepare: SQL-Statement vorbereiten
								$PDOStatement = $PDO->prepare($sql);
								
								// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
								$PDOStatement->execute($params);
								
							} catch(PDOException $error) {
if(DEBUG) 					echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
								$dbError = 'Fehler beim Zugriff auf die Datenbank!';
							}
					
							// Schritt 4 DB: Datenbankoperation auswerten


							$rowCount = $PDOStatement->rowCount();
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";
															
							if( $rowCount !== 1 ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Speichern der neuen Kategorie in die DB! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
																
							} else {
								
								$newCatLabelID = $PDO->lastInsertID();
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die neue Kategorie wurde erfolgreich unter ID: $newCatLabelID in die DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";				
		
								// Erfolgsnachricht für den User
								$dbSuccess = "Die neue Kategorie mit dem Namen '$newCatLabelForm' wurde erfolgreich gespeichert.";


								// Werte im Input zurücksetzen
								$newCatLabelForm = NULL;

							} // 2. SAVE CATLABEL DATA INTO DB ENDS HERE

						} // 1. CHECK IF CATEGORIE ALREADY EXITS ENDS HERE

						// DB-Verbindung schließen
if(DEBUG_DB)		echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung wird geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
						unset($PDO);

					} // FINAL FORM VALIDATION (FIELDS VALIDATION) ENDS HERE

				} // PROCESS FORM CREATE CATLABEL DATA ENDS HERE


#****************************************************************************************************#


				#**************************************************#
				#********** FETCH CATEGORIE DATA FROM DB **********#
				#**************************************************#


				// Schritt 1 DB: Verbindung zur Datenbank aufbauen:
				$PDO = dbConnect(DB_NAME);
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Kategorien aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";


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

#****************************************************************************************************#


				#***************************************************#
				#********** PROCESS FORM CREATE BLOG DATA **********#
				#***************************************************#
				
				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/
				#****************************************#

				// Schritt 1 FORM: Prüfen, ob das Formular veschickt wurde
				if( isset($_POST['formCreateBlogData']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: Formular 'Create Blog Data' wurde abgeschickt. <i>(" . basename(__FILE__) . ")</i></p>\n";										
										
				// Schritt 2 FORM: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Formularwerte
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";


					$catIDForm		 	= sanitizeString( $_POST['f2'] );
					$headlineForm		= sanitizeString( $_POST['f3'] );
					$alignPicForm		= sanitizeString(	$_POST['f4'] );
					$contentForm		= sanitizeString( $_POST['f5'] );

if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$catIDForm: $catIDForm <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$headlineForm: $headlineForm <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$alignPicForm: $alignPicForm <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$contentForm: $contentForm <i>(" . basename(__FILE__) . ")</i></p>\n";

					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>:Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
				
					
					$errorCatIDForm 		= validateInputString($catIDForm, minLength: 1);
					$errorHeadlineForm	= validateInputString($headlineForm);	
					$errorAlignPicForm	= validateInputString($alignPicForm, maxLength: 10);
					$errorContentForm		= validateInputString($contentForm, maxLength: 5000);

					#********** FINAL FORM VALIDATION I (FIELDS VALIDATION) **********#



					if( $errorCatIDForm  !== NULL OR $errorHeadlineForm !== NULL OR 
						 $errorAlignPicForm !== NULL OR $errorContentForm !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION I: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION I: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
					
						#****************************************#
						#********** IMAGE UPLOAD START **********#
						#****************************************#
/*				
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_FILES <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($_FILES);					
if(DEBUG_V)			echo "</pre>";
*/

						#********** CHECK IF IMAGE UPLOAD IS ACTIVE **********#
						if( $_FILES['f6']['tmp_name'] === '' ) {
							// Image Upload inactive
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Image Upload inaktiv. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
						} else {
							// Image upload active
if(DEBUG)				echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Image Upload aktiv. <i>(" . basename(__FILE__) . ")</i></p>\n";				
				
							$validateImageUploadReturnArray = validateImageUpload( $_FILES['f6']['tmp_name'] );

/*					
if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$validateImageUploadReturnArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)				print_r($validateImageUploadReturnArray);					
if(DEBUG_V)				echo "</pre>";								
*/					
			
							#********** VALIDATE IMAGE UPLOAD **********#
							if( $validateImageUploadReturnArray['imageError'] !== NULL ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Bildupload: $validateImageUploadReturnArray[imageError]! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
								$errorImageUpload = $validateImageUploadReturnArray['imageError'];
									
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Bild erfolgreich nach <i>'$validateImageUploadReturnArray[imagePath]' auf den Server geladen.</i>. <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
								$blogImagePathForm = $validateImageUploadReturnArray['imagePath'];

							} // VALIDATE IMAGE UPLOAD ENDS HERE

						} 
						#********** IMAGE UPLOAD ENDS HERE **********#
							
						#********** FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION) **********#
						if( $errorImageUpload !== NULL ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II: Das Formular ist komplett fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
							#********** UPLOAD BLOG DATA TO DB **********#

							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#

							// Schritt 1 DB: DB-Verbindung herstellen
							$PDO = dbConnect(DB_NAME);
						
							// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:

							$sql 	=	'INSERT INTO blogs
										(blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID)
										VALUES
										(:blogHeadline, :blogImagePath, :blogImageAlignment, :blogContent, :catID, :userID)';
			
							$params 	= array( 'blogHeadline'			=> $headlineForm,
													'blogImagePath' 		=> $blogImagePathForm,
													'blogImageAlignment'	=> $alignPicForm,
													'blogContent' 			=> $contentForm,
													'catID' 					=> $catIDForm,
													'userID' 				=> $userID );


							// Schritt 3 DB: Prepared Statements:

							try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
									
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($params);
									
							} catch(PDOException $error) {
if(DEBUG) 					echo "<p class='debug db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
								$dbError = 'Fehler beim Zugriff auf die Datenbank!';
							}

							// Schritt 4 DB: Datenbankoperation auswerten und DB-Verbindung schließen

							$rowCount = $PDOStatement->rowCount();
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";
															
							if( $rowCount !== 1 ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Speichern der Registrierungsdaten in die DB! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
																
							} else {

								$newBlogID = $PDO->lastInsertID();
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Create Blog Daten erfolgreich unter ID: $newBlogID in die DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";	

								$dbSuccess = 'Der Beitrag wurde erfolgreich gespeichert.';

								// Input Werte zurücksetzen
								$headlineForm		 	= NULL;
								$blogImagePathForm 	= NULL;
								$alignPicForm 			= NULL;
								$contentForm 			= NULL;
								$catIDForm 				= NULL;


							} // UPLOAD BLOG DATA TO DB ENDS HERE

						} // FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION) ENDS HERE

					} // FINAL FORM VALIDATION I (FIELDS VALIDATION) ENDS HERE

				} // PROCESS FORM CREATE BLOG DATA ENDS HERE
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
		
		</style>
		
	</head>
	
	<body>
		
		<!-- -------- PAGE HEADER -------- -->
		<header class="fright loginheader">
			<p><a href="?action=logout"><< Logout</a></p>
			<p><a href="index.php"><< zum Frontend</a></p>
		</header>
		<div class="clearer"></div>
		
		<!-- -------- PAGE HEADER END -------- -->

		<!-- -------- USER MESSAGES START -------- -->
		<?php if( isset($dbError) === true ): ?>
			<h3 class="error"><?= $dbError ?></h3>
		<?php elseif( isset($dbInfo) === true ): ?>
			<h3 class="info"><?= $dbInfo ?></h3>
		<?php elseif( isset($dbSuccess) === true ): ?>
			<h3 class="success"><?= $dbSuccess ?></h3>
		<?php endif ?>
		<!-- -------- USER MESSAGES END -------- -->		



		
		<h1>PHP - Projekt Blog - Dashboard</h1>
		<p>Aktiver Benutzer: <?= $userFirstName . ' ' .  $userLastName ?></p>
		
		<main class="fleft">

			<h3>Neuen Blog-Eintrag verfassen</h3>
			<div class="clearer"></div>

			<br>
	
			
			<!-- -------- FORM CREATE BLOG DATA START -------- -->
		
			<form action="" method="POST" enctype="multipart/form-data">
				
				<input type="hidden" name="formCreateBlogData">
				
					
				<!-- -------- SELECT BOX CATGORIES START -------- -->
				
			
				<select class="categories" name="f2">
   				<?php foreach ($categoriesArray as $category): ?>
       				<option value="<?= $category['catID'] ?>" <?php if ($catIDForm == $category['catID']) echo 'selected' ?>>
         				<?= $category['catLabel'] ?>
        				</option>
    				<?php endforeach ?>
				</select>

				<br>
				<br>
				
				<!-- -------- SELECT BOX CATGORIES END -------- -->


				<!-- -------- INPUT FIELD HEADLINE START -------- -->		
				<span class="error"><?= $errorHeadlineForm ?></span><br>
				<input type="text" name="f3" value="<?= $headlineForm ?>" placeholder="Überschrift"><br>
				
				<!-- -------- INPUT FIELD HEADLINE END -------- -->		
				<br>
				<br>
					
				<!-- -------- FILE UPLOAD FIELD START -------- -->
				<label>Bild hochladen:</label>
				<br>
			
					<span class="error"><?= $errorImageUpload ?></span><br>
					<div class="fileUpload">
						<input type="file" name="f6">
						<select class="alignPicture" name="f4">
							<option value="left" <?php if( $alignPicForm === 'left' ) echo 'selected' ?>>align left</option>
							<option value="right" <?php if( $alignPicForm === 'right' ) echo 'selected' ?>>align right</option>
						</select>
					</div>
				
					<?php if( $errorImageUpload !== NULL ) : ?>
						<p class="small">
							Erlaubt sind Bilder des Typs 
							<?php $imageAllowedMimeTypes = implode(', ', array_keys(IMAGE_ALLOWED_MIME_TYPES)) ?>
							<?= strtoupper( str_replace( array('image/jpeg, ', 'image/'), '', $imageAllowedMimeTypes) ) ?>.
							<br>
							Die Bildbreite darf <?= IMAGE_MAX_WIDTH ?> Pixel nicht übersteigen.<br>
							Die Bildhöhe darf <?= IMAGE_MAX_HEIGHT ?> Pixel nicht übersteigen.<br>
							Die Dateigröße darf <?= IMAGE_MAX_SIZE/1024 ?>kB nicht übersteigen.
						</p>
					<?php else : ?>
						<br>
						<br>
					<?php endif ?>
			
				<!-- -------- FILE UPLOAD FIELD END -------- -->
				
				
		
				
				<!-- -------- TEXTAREA START -------- -->
				<span class="error"><?= $errorContentForm ?></span><br>
				<textarea class="fleft" name="f5" placeholder="Text..."><?= $contentForm ?></textarea>
				<div class="clearer"></div>
				<br>
				<!-- -------- TEXTAREA END -------- -->
					
				<br>
					
				<input type="submit" value="Veröffentlichen">
						
			</form>			
			<!-- -------- FORM CREATE BLOG DATA END -------- -->
			
		</main>
		
		<aside class="fright">
			<h3>Neue Kategorie anlegen</h3>

		

			<!-- -------- FORM CREATE CATEGORIE DATA START -------- -->
			<form action="" method="POST">
				<input type="hidden" name="formCreateCatLabelData">

				<span class="error"><?= $errorNewCatLabelForm ?></span><br>
		
				<input type="text" name="f1" value="<?= $newCatLabelForm ?>" placeholder="Name der Kategorie">
				<br>
				<br>
				<input type="submit" value="Neue Kategorie anlegen">
			</form>
			<!-- -------- FORM CREATE CATEGORIE DATA END -------- -->
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
		<br>
		<br>
		<br>
		
	</body>
	
</html>