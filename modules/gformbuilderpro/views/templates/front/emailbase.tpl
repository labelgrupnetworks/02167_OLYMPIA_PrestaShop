{*
* Do not edit the file if you want to upgrade the module in future.
* 
* @author    Globo Software Solution JSC <contact@globosoftware.net>
* @copyright 2015 GreenWeb Team
* @link	     http://www.globosoftware.net
* @license   please read license in file license.txt
*/
*}

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		<title>
        {if isset($reply) && $reply == 1}
            {literal}{reply_subject}{/literal}
        {else}
            {$subject|escape:'html':'UTF-8'}
        {/if}
        </title>
	</head>
	<body>
		{if isset($isps17) && $isps17}
        	{$emailcontent nofilter}{* $emailcontent is html content, no need to escape*}
		{else}
			{$emailcontent}{* $emailcontent is html content, no need to escape*}
		{/if}
	</body>
</html>