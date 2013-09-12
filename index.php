<?php
/**
 * Templateing Enging
 *
 * Copyright 2013 Thomas St.Pierre. All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 * 
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY Thomas St.Pierre ''AS IS'' AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Thomas St.Pierre OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

 * The views and conclusions contained in the software and documentation are those of the
 * authors and should not be interpreted as representing official policies, either expressed
 * or implied, of Thomas St.Pierre.
 *
 */

/*
 * 
 * If we haven't selected a template yet, search current directory for templates and provide link to each
 * 
 */

if (!isset($_REQUEST['template']))
{
	print ("Please select one of the following templates:\n<br><br>");
	$templates = glob("*.tpl");
	foreach ($templates as $file)
	{
		print ("<a href='".$_SERVER['PHP_SELF']."?template=".$file."'>$file</a><br>\n");
	}
	exit;
}


/*
 * 
 * Read in the template file and split it into XML and template parts
 * 
 */

$template_file = $_REQUEST['template'];
if (!file_exists($template_file))
{
	print ("Error finding template file"); exit;
}

$entire_template = file_get_contents($template_file);

list($template_raw_xml, $template) = explode("--BEGIN-TEMPLATE--",$entire_template);


/*
 * Parse XML (should add error checking or something :P)
 */

$xml = simplexml_load_string(trim($template_raw_xml));

/*
 * 
 * Print out the form based on questions in the XML
 * 
 */
if (!isset($_REQUEST['submitted']))
{
	// declare the form
	print ("<FORM ACTION=". $_SERVER['PHP_SELF'] ." METHOD=POST>\n");
	print ("<INPUT TYPE=HIDDEN NAME=template VALUE='".$_REQUEST['template']."'>\n"); // resend the template we're using
	
	foreach ($xml as $question)
	{
		switch ($question['type'])
		{
			case 'simple':
				print ($question['description']." : <INPUT TYPE=TEXT NAME='".$question['shortname']."'><BR>\n");
				break;
	
			case 'switch':
				print ($question['description']." : <SELECT NAME='".$question['shortname']."'>\n");
				foreach ($question as $option)
				{
					print("<OPTION VALUE='".$option['shortname']."'");
					if ($option['default'] == 1) { print (" SELECTED"); }
					print(">".$option['description']."</OPTION>\n");
				}
				print ("</SELECT><BR>\n");
				break;

			case 'checkbox':
				print ($question['description']." : <INPUT TYPE=checkbox NAME='".$question['shortname']."'><BR>\n");
				break;


		}

	}
	print ("<INPUT TYPE=SUBMIT name=submitted>\n</FORM>");
	exit;
}

/*
 * 
 * If we got here it's because the form was submitted, so lets apply it to the template!
 * 
 */
foreach ($xml as $question)
{
	$question_shortname = (string)$question['shortname']; // for use later, depending on use $question['shortname'] can return an object rather then the text
	
	switch ($question['type'])
	{
		case 'simple':
			$template = str_replace('[% '. $question->match .' %]', $_REQUEST[$question_shortname], $template );
			break;

		case 'switch':
			foreach ($question as $option)
			{
				if ($option['shortname'] == $_REQUEST[$question_shortname])
				{
					foreach ($option as $block)
					{
						$template = str_replace('[% '. $block->match .' %]', $block->replace, $template );
					}
				}
			}
			break;

		case 'checkbox':
			if (isset($_REQUEST[$question_shortname]))
			{
				$template = str_replace('[% '. $question->match .' %]', $question->replace, $template );
			} else
			{
				$template = str_replace('[% '. $question->match .' %]', '', $template );
			}
			break;


	}


}

/*
 * 
 * Print out the template
 * 
 */

print ("<PRE>$template</PRE>");

?>
