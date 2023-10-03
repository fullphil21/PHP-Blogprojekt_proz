<?php
#****************************************************************************************************#
				
				
				#***************************************************#
				#********** GLOBALE PROJECT CONFIGURATION **********#
				#***************************************************#
			
				
				#********** DATABASE CONFIGURATION **********#
				define('DB_SYSTEM',							'mysql');
				define('DB_HOST',								'localhost');
				define('DB_NAME',								'blogprojekt');
				define('DB_USER',								'root');
				define('DB_PWD',								'');
				
				
				#********** EXTERNAL INPUT STRING CONFIGURATION **********#
				define('INPUT_MAX_LENGTH',	256);
				define('INPUT_MIN_LENGTH',	0);
				
				
				#********** IMAGE UPLOAD CONFIGURATION **********#
				define('IMAGE_MAX_WIDTH',					800);
				define('IMAGE_MAX_HEIGHT',					800);
				define('IMAGE_MIN_SIZE',					1024);
				define('IMAGE_MAX_SIZE',					128*1024);
				define('IMAGE_ALLOWED_MIME_TYPES',		array('image/jpeg'=>'.jpg', 'image/jpg'=>'.jpg', 'image/gif'=>'.gif', 'image/png'=>'.png'));
				
				
				#********** STANDARD PATHS CONFIGURATION **********#
				define('IMAGE_UPLOAD_PATH',				'./uploaded_images/');
				
				
				#********** DEBUGGING **********#
				define('DEBUG', 				true);				// Debugging for main document
				define('DEBUG_V', 			true);				// Debugging for values
				define('DEBUG_F', 			true);				// Debugging for functions
				define('DEBUG_DB', 			true);				// Debugging for DB operations


#****************************************************************************************************#