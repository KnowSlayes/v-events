<?php
	/**
	 * Template System
	 *
	 * @copyright	Copyright (c) 2020, Marc-Andre Zweier
	 * @author 		Marc-Andre Zweier <marc.zweier@gmail.com>
	 * @version   	0.4.1
	 * @package    	Template System
	 * @category	Plugin
	 *
	 *
	 * !!!!!!!!!!!!!!! IMPORTANT NOTE !!!!!!!!!!!!!!!!!!
	 * !               °°°°°°°°°°°°°°                  !
	 * !        Please do not change this file.        !
	 * ! Changes can cause the system to stop working. !
	 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	 *
	 * Conection and function to the database system
	 */ 

	/**
	 * ToDos
	 * °°°°°
	 *
	 * #0001 Bootstrap Modus abfragen (Aktiv werden Bootstrap Datei automatisch mit geladen)
	 * #0002 assignHTML Tags abfragen und bei verwendung von nicht gültigen ggf. Log Datei schreiben
	 * #0003 Replace HTML Meta und co Block zu einer Funktion zusammen schreiben (Weiderholt sich)
	 * #0004 CSS und JS auch mit alternativen Ordner aufrufen
	 * #0005 Fehlerausgabe falls HTML Site nicht geladen werden kann
	 * #0006 Fehlerausgabe falls CSS oder JS datei nicht vorhanenden ist
	 * #0007 Erkennung von : aus der Sprachdatei
	 * #0008 Sprachdateien ohne [ISO-Land] einen Landzuweisen und Fehler erkennung bauen
	 */

	class Template extends Core_Function {
		/** 
		 * Constants of the class
		 *
		 * 	- Version informations
		 * 	- Default folder informatios
		 */
		const CLASS_VERSION	= '0.0.1-beta';
		const CLASS_BUILD	= '00001';
		const CLASS_DATE	= '2021-02-08';

		const DIR_TEMPLATE	= 'templates';
		const DIR_LANGUAGE	= 'language';
		
		const DEFAULT_LANGUAGE = 'de';

		/**
		 * Placeholder
		 */
		// HtmlTags { <NAME> }
		private const leftDelimiter  = '\{';		// Left Delimter (Special characters must be escaped)
		private const rightDelimiter = '\}';		// Right Delimter (Special characters must be escaped)
		// Comments {# <TEXT> }
		private const leftDelimiterC  = '\{\#';		// Left Delimter (Special characters must be escaped)
		private const rightDelimiterC = '\}';		// Right Delimter (Special characters must be escaped)
		// placeholderVar and langVar {$ <VARIBALE> }
		private const leftDelimiterV  = '\{\$';		// Left Delimter (Special characters must be escaped)
		private const rightDelimiterV = '\}';		// Right Delimter (Special characters must be escaped)
		// systemVar % <TEXT> %
		private const leftDelimiterS  = '\%';		// Left Delimter (Special characters must be escaped)
		private const rightDelimiterS = '\%';		// Right Delimter (Special characters must be escaped)

		// Template File {{ <FILE> }}
		// private const leftDelimiterP  = '\{\{';		// Left Delimter (Special characters must be escaped)
		// private const rightDelimiterP = '\}\}';		// Right Delimter (Special characters must be escaped)
		// DataBlock {: <NAME> :}
		// private const leftDelimiterD = '\{\:';	// Left Delimter (Special characters must be escaped)
		// private const rightDelimiterD = '\:\}';	// Right Delimter (Special characters must be escaped)

		/**
		 * Variables for the Files
		 *
		 * 	Site 		-> Basic HTML Siite (Folder not changeable by load function)
		 * 	Template -> Template File to be integrated (Path can be change)
		 * 	Language -> Language File to be integrated  (Path can be change)
		 */
		private $templateDir;		// Template Folder
		private $templateFile;		// Template Filename
		private $templatePath;		// Template Complete Path
		private $languageDir;		// Language Folder
		private $languageFile;		// Language Filename
		private $languagePath;		// Language Complete Path
		
		/**
		 * Arraiy and Variabels for the System
		 */
		private $assignHtmlMetadata = array();	// 
		private $assignHtmlCSSfiles = array();	// 
		private $assignHtmlJSHfiles = array();	// 
		private $assignHtmlJSBfiles = array();	// 
		private $assignHtmlLang = '';			//
		private $assignHtmlTitle = '';			//
		private $assignHtmlFavicon = '';		//

		private $placeholderVar = array();		// 
		private $langVar = array();				// 
		private $systemVar = array();			// 
		private $tploutputcache;				//
		
		private $language = '';					//


		/** 
		 * Start of Class
		 *
		 * 	Set Folder Information from the Default or Custom Information
		 */
		public function __construct($f_DIRtemplate = "", $f_DIRlanguage = "") {
			if (!empty($f_DIRtemplate)) {
				$this->templateDir = $this->chkSeparator($f_DIRtemplate);
			} else {
				$this->templateDir = $this->chkSeparator($this::DIR_TEMPLATE);
			}
			if (!empty($f_DIRlanguage)) {
				$this->languageDir = $this->chkSeparator($f_DIRlanguage);
			} else {
				$this->languageDir = $this->chkSeparator($this::DIR_LANGUAGE);
			}
			$this->language = $this::DEFAULT_LANGUAGE;
		}
	/*####### Set & Get functions #######*/



	/*####### Load & Read functions #######*/
		/**
		 * HTML Site load Function
		 */
		public function load($f_file='w3c_std.html') {
			$this->templateFile = $f_file;
			$this->templatePath = $this->templateDir.$this->templateFile;
			if( file_exists($this->templatePath) ) {
				$this->tploutputcache = file_get_contents($this->templatePath);
			} else {
				DisplayError("Template File not found!",'File not found: '.$this->templatePath);
				return false;
			}
		}


		/**
		 * Read Language File sort by Language all assignVar and translation
		 */
		public function readLanguage($f_filename, $f_dir='') {
			if (!empty($f_dir)) { 
				$this->languageDir =  $this->chkSeparator($f_dir); 
			}
			$this->languageFile = $f_filename;
			$this->languagePath = $this->languageDir.$this->languageFile;
			if(file_exists($this->languagePath)) {
				$temp_file = file_get_contents($this->languagePath);
				$langvariablesArray = preg_split('/(\[[^]](.*)[^\/]])/i', $temp_file, -1, PREG_SPLIT_NO_EMPTY );							  
				preg_match_all('@\[(.*)\]@', $temp_file, $langletterArray);
				if (count($langvariablesArray)!=count($langletterArray)){
					echo "Error in Language File (".$this->languagePath.")"; #MZ: internes Lyout nutzen
					return false;
				}
				$i=0;
				foreach ($langvariablesArray as $langassign) {
					$langletter = $langletterArray[1][$i];
					$explode_langlangassign = explode(";", $langassign);
					foreach ($explode_langlangassign as $row_languageassign) {
						if(strpos($row_languageassign,":")!==false){
							$explode_row_languageassign = explode(":", $row_languageassign);
							$temp_VarKey = trim($explode_row_languageassign[0]); 
							$temp_VarValue = preg_replace('/\<(.*)\>/isU', '', trim($explode_row_languageassign[1]));
							//$temp_VarValue = trim($explode_row_languageassign[1]); #MZ: Alle HTML Tags entfernen
							$this->langVar[$langletter][$temp_VarKey]=$temp_VarValue;
						}
					}
					$i++;
				}
			} else {
				DisplayError("Languange File not found!",'File not found: '.$this->languagePath);
				return false;
			}	
		}



	/*####### Assiging function #######*/
		/**
		 * Assigning HTML Head and Body data 
		 */
		public function assignHTML($f_type, $f_opt1, $f_opt2='', $f_opt3='', $f_opt4='') {
			if ($f_type=='lang')  { $this->assignHtmlLang = $f_opt1; }
			if ($f_type=='title')  { $this->assignHtmlTitle = $f_opt1; }
			if ($f_type=='favicon') { $this->assignHtmlFavicon = $f_opt1; }
			if ($f_type=='meta'){ $this->assignHtmlMetadata[$f_opt1] = $f_opt2; }	// Head [name] = content)
			if ($f_type=='css') {
				$this->assignHtmlCSSfiles[$f_opt1]['intgerity'] = $f_opt2; 			// CSS [filename] -> intgerity
				$this->assignHtmlCSSfiles[$f_opt1]['corssorigin'] = $f_opt3; 		// CSS [filename] -> corssorigin
			}
			if ($f_type=='js') { 
				if ($f_opt2=='body') {								
					$this->assignHtmlJSBfiles[$f_opt1]['intgerity'] = $f_opt3;		// JS into the Body Area [filename] -> intgerity
					$this->assignHtmlJSBfiles[$f_opt1]['corssorigin'] = $f_opt4;	// JS into the Body Area [filename] -> corssorigin
				} else {
					$this->assignHtmlJSHfiles[$f_opt1]['intgerity'] = $f_opt3;		// JS into the Head Area [filename] -> intgerity
					$this->assignHtmlJSHfiles[$f_opt1]['corssorigin'] = $f_opt4;	// JS into the Head Area [filename] -> corssorigin
				}
			}
		}


		/*
		 * Assignment for diffrent types
		 *
		 * 	default: 	placeholderVar
		 * 	Option:
		 *		-s		systemVar (from intera Functions Vars)
		 */
		public function assign($f_placeholder, $f_replacement, $f_type="default") {
			switch ($f_type) {
				case "default":							// placeholderVar Array
					$this->placeholderVar[$f_placeholder] = $f_replacement;
					break;
				case "system"; case "s":				// SystemVar Array
					$this->systemVar[$f_placeholder] = $f_replacement;
					break;
			}
		}
		



	/*####### parseHTMLassign #######*/
		/**
		 * preg_Replace for HTML assignment
		 */
		private function pregreplace_assignmentHTML ($f_placeholder, $f_replacement) {
			$this->tploutputcache = preg_replace('/'.$this::leftDelimiter.$f_placeholder.$this::rightDelimiter.'/isU', 
												 $f_replacement,
												 $this->tploutputcache ); 
		}


		/**
		 * Replacment function for HTML assignment
		 */
		private function replace_HTMLlang() {
			$temp_langdata = "";
			if (!empty($this->assignHtmlLang)) {
				$temp_langdata = ' lang="'.$this->assignHtmlLang.'"';
			}
			$this->pregreplace_assignmentHTML('HTML_LANG',$temp_langdata);
		}
		private function replace_HTMLtitle() {
			$temp_titledata = "";
			if (!empty($this->assignHtmlTitle)) {
				$temp_titledata = '<title>'.$this->assignHtmlTitle.'</title>'."\n\t\t";;
			}
			$this->pregreplace_assignmentHTML('HTML_TITLE',$temp_titledata);
		}
		private function replace_HTMLfavicon() {
			$temp_favicondata = "";
			if (!empty($this->assignHtmlFavicon)) {
				$temp_favicondata = '<link rel="shortcut icon" type="image/x-icon" href="'.$this->assignHtmlFavicon.'" />';
			}
			$this->pregreplace_assignmentHTML('HTML_FAVICON',$temp_favicondata);
		}
		private function replace_HTMLmeta() {
			$temp_metadata="";
			foreach ($this->assignHtmlMetadata as $array_key => $array_value) {
				$temp_metadata .= '<meta name="'.$array_key.'" content="'.$array_value.'" />'."\n\t\t";
			}
			$this->pregreplace_assignmentHTML('HTML_META',$temp_metadata);
		}
		private function replace_HTMLcss() {
			$temp_cssfile="";
			foreach ($this->assignHtmlCSSfiles as $array_key => $array_value) {
				$temp_cssintgerity = "";
				$temp_csscrossorigin = "";
				if (!empty($array_value['intgerity'])) 	{ $temp_cssintgerity   = ' integrity="'.$array_value['intgerity'].'"'; }
				if (!empty($array_value['corssorigin']))  { $temp_csscrossorigin = ' crossorigin="'.$array_value['corssorigin'].'"'; }
				$temp_cssfile .= '<link rel="stylesheet" type="text/css" href="'.$array_key.'"'.$temp_cssintgerity.$temp_csscrossorigin.' />';
				if ($array_key != array_key_last($this->assignHtmlCSSfiles)) { $temp_cssfile .= "\n\t\t";}
			}
			$this->pregreplace_assignmentHTML('HTML_CSS',$temp_cssfile);
		}
		private function replace_HTMLjs() {
			$temp_jsfile="";
			foreach ($this->assignHtmlJSHfiles  as $array_key => $array_value) {
				$temp_jsintgerity = "";
				$temp_jscrossorigin = "";
				if (!empty($array_value['intgerity'])) 	{ $temp_jsintgerity   = ' integrity="'.$array_value['intgerity'].'"'; }
				if (!empty($array_value['corssorigin']))  { $temp_jscrossorigin = ' crossorigin="'.$array_value['corssorigin'].'"'; }
				$temp_jsfile .= '<script src="'.$array_key.'"'.$temp_jsintgerity.$temp_jscrossorigin.'></script>';
				if ($array_key != array_key_last($this->assignHtmlJSHfiles)) { $temp_jsfile .= "\n\t\t";}
			}
			$this->pregreplace_assignmentHTML('HTML_JSH',$temp_jsfile);
			$temp_jsfile="";
			foreach ($this->assignHtmlJSBfiles  as $array_key => $array_value) {
				$temp_jsintgerity = "";
				$temp_jscrossorigin = "";
				if (!empty($array_value['intgerity'])) 	{ $temp_jsintgerity   = ' integrity="'.$array_value['intgerity'].'"'; }
				if (!empty($array_value['corssorigin']))  { $temp_jscrossorigin = ' crossorigin="'.$array_value['corssorigin'].'"'; }
				$temp_jsfile .= '<script src="'.$array_key.'"'.$temp_jsintgerity.$temp_jscrossorigin.'></script>';
				if ($array_key != array_key_last($this->assignHtmlJSBfiles)) { $temp_jsfile .= "\n\t\t";}
			}
			$this->pregreplace_assignmentHTML('HTML_JSB',$temp_jsfile);			
		}


	/*####### parseFunctions #######*/
		/**
		* Delete all comments from the output
		*/
		private function replace_coments() {
			$this->tploutputcache = preg_replace('/' .$this::leftDelimiterC .'(.*)' .$this::rightDelimiterC .'/isU',
												 '',
												 $this->tploutputcache);
		}











	/*####### parsePlaceholder #######*/
		/**
		 * preg_Replace Function for the placeholderVar Array
		 */
		private function pregcallback_placeholderVar($f_matches_placeholderHTML) {
			if (isset($this->placeholderVar)){
				if (array_key_exists($f_matches_placeholderHTML[1], $this->placeholderVar)) {
					return $this->placeholderVar[$f_matches_placeholderHTML[1]];
				} else {
					return $f_matches_placeholderHTML[0];
				}
			} else {
					return $f_matches_placeholderHTML[0];
			}
		}
		/**
		 * Replace any placeholder variable for the length of the placeholderVar array
		 */
		private function replace_placeholderVar() {	
			$this->tploutputcache = preg_replace_callback('/'.$this::leftDelimiterV.'(.*)'.$this::rightDelimiterV.'/isU', 
														  array($this, 'pregcallback_placeholderVar'),
														  $this->tploutputcache);
		}
		/**
		 * preg_Replace Function for the systeVar Array
		 */
		private function pregcallback_systemVar($f_matches_SystemVar){
			if (isset($this->systemVar)){
				if (array_key_exists($f_matches_SystemVar[1], $this->systemVar)) {
					return $this->systemVar[$f_matches_SystemVar[1]];
				} else {
					return $f_matches_SystemVar[0];
				}
			} else {
					return $f_matches_SystemVar[0];
			}
		}
		/**
		 * Replace any systemvariabel placeholder for the length of the AssignVar array
		 */
		private function replace_systemVar() {	
			$this->tploutputcache = preg_replace_callback('/'.$this::leftDelimiterS.'(.*)'.$this::rightDelimiterS.'/isU',
														  array($this, 'pregcallback_systemVar'),
														  $this->tploutputcache);
		}










		/**
		 * preg_Replace Function for the langVar Array
		 */
		private function pregcallback_langVar($f_matches_placeholderVar){
			if (isset($this->langVar[$this->language])){
				if (array_key_exists($f_matches_placeholderVar[1], $this->langVar[$this->language])) {
					return $this->langVar[$this->language][$f_matches_placeholderVar[1]];
				} else {
					return $f_matches_placeholderVar[0];
				}
			} else {
					return $f_matches_placeholderVar[0];
			}
		}

		/**
		 * Replace any placeholder variable for the length of the langVar array (Language function)
		 */
		private function replace_LangVars() {	
			$this->tploutputcache = preg_replace_callback('/'.$this::leftDelimiterV.'(.*)'.$this::rightDelimiterV.'/isU',
														  array($this, 'pregcallback_langVar'),
														  $this->tploutputcache);
		}		
		




	/*####### Analyse #######*/
		/**
		 * Anlayse Block for all Assignment
		 * 
		 * HTMLassign 	-> all HTML TAGS (LANG, META, CSS, JS) assigment
		 * Function		-> Remove Comentars,
		 * Placeholder	-> all Vars
		 */
		private function parseHTMLassign() {
			$this->replace_HTMLlang();
			$this->replace_HTMLtitle();
			$this->replace_HTMLfavicon();
			$this->replace_HTMLmeta();
			$this->replace_HTMLcss();
			$this->replace_HTMLjs();
		}
		private function parseFunctions() {
			$this->replace_coments();
			// more stuff
		}
		private function parsePlaceholder() {
			$this->replace_placeholderVar();
		 	$this->replace_LangVars();	
		 	$this->replace_systemVar();
		}


		/**
		* Analyse from all Assignment
		*/
	 	private function parsen() {
			$this->parseHTMLassign();
			$this->parseFunctions();
			$this->parsePlaceholder();
		}


		/*
		 * Analyse all assignment and print out the Template Output Cache
		 */
		public function display() {
			$this->parsen();
			echo $this->tploutputcache;
		}		
































		





		

		

		###### Public Function




		/**
		 * Load Template File for the Placeholder {{<NAME>}}
		 */
		public function assignTemplate($f_searchterm, $f_filename, $f_langauge="") {
			$this->templateFile = $f_filename;
			$this->templatePath = $this->templateDir.$this->templateFile;
			if (!empty($f_langauge)){
				#MZ: Vergleichen der Eingabe mit ISO Sprachdatei liste
				$this->language = $f_langauge;
			} else {
				$this->language = $this::DEFAULT_LANGUAGE;
			}
			$this->assign('LANGUAGE',$this->language,'s');
			if (file_exists($this->templatePath)){
				$temp_file = file_get_contents($this->templatePath);
				$this->tploutputcache = preg_replace('/'.$this::leftDelimiterP.$f_searchterm.$this::rightDelimiterP.'/isU',
													 $temp_file,
													 $this->tploutputcache);
			} else {
				echo "not exits (".$this->templatePath.")"; #MZ: internes Lyout nutzen
				return false;
			} 
		}		



		// NAMEN GEBEN
		// HTML Code ist das sich wiederholende Design
		// Datablock ist 
		public function HTML_Datacblock ($f_htmlcode, ...$f_datablock){
			if(file_exists($f_htmlcode)) {
				$temp_datablock = file_get_contents($f_htmlcode);
			} else {
				$temp_datablock = 'ERR FILE NOT FOUND';
			}
			foreach ($f_datablock as $n) {
				//echo $n.":-:";
				$explode_datablock = explode("|", $n);
				//echo "(exp: ".$explode_datablock[0]."->".$explode_datablock[1].")<br>";
				$temp_datablock = preg_replace('/' .$this::leftDelimiterD .$explode_datablock[0].$this::rightDelimiterD .'/isU',
												 $explode_datablock[1],
												 $temp_datablock); 
			}
			return $temp_datablock ;
		}


	}
?>