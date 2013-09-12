<xml>
	<!-- 
	
	Simple question type, will create a text input, and replace the match with what was entered 
	
		shortname = value that will be used in "NAME" attribut of form element to identify this question
		description = Text shown on form to describe desired input.
	-->
	<question type="simple" shortname="hostname" description="Hostname">
		<!-- Whereever this string is found in template (Surrounded by [% and %]) we will 
		replace it with the text entered in the form -->
		<match>HOSTNAME</match>
	</question>


	<!-- 
	
	Question type switch, will create a drop down, and let you choose between several options 
	
		shortname = value that will be used in "NAME" attribut of form element to identify this question
		description = Text shown on form to describe desired input.
	-->
	<question type="switch" shortname="mgmt_net" description="Management Network">
		<!-- 
			Define's an availible option in the dropdown, shortname and description like for questions.
		 -->
		<option shortname="corp" description="Corporate">
			<!-- 
				If this option is selected in the form, we will go through each block, and replace the string matched 
				in the match element (surrounded by [% and %], with the contents of the replace element.
			-->
			<block>
				<match>MANAGEMENT_VLAN</match>
				<replace>1000</replace>
			</block>
			<block>
				<match>GATEWAY</match>
				<replace>192.168.50.1</replace>
			</block>
		</option>
		<option shortname="prod" description="Production">
			<block>
				<match>MANAGEMENT_VLAN</match>
				<replace>2000</replace>
			</block>
			<block>
				<match>GATEWAY</match>
				<replace>192.168.60.1</replace>
			</block>
		</option>
	</question>
	
	
	<!-- 
		Question type Checkbox, if checked will replace string in match element (Surrounded by [% and %]) with
		contents of replace element. If not checked, will replace with nothing (will remove string in match element
		from template
	 -->	
	<question type="checkbox" shortname="service_encrypt" description="Password Encryption">
		<match>PW_ENCRYPTION</match>
		<replace>service password-encryption</replace>
	</question>
</xml>
--BEGIN-TEMPLATE--

hostname [% HOSTNAME %]

[% PW_ENCRYPTION %]

vlan [% MANAGEMENT_VLAN %]
 name MANAGEMENT_VLAN

ip default gateway [% GATEWAY %]
 



