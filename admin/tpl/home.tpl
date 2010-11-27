{* $Id$ *}
{extends file="components/main.tpl"}
{block name="content"}

<h2>Welcome, {$smarty.session.username}</h2>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td valign="top">
            <p>You can now administer the MagIRC configuration.<br />Please select a settings category from the left menu.</p>
            {if $setup}
            <div class="warning">
                <p>As a safety measure, please<br /><strong>remove the <em>setup/</em> directory</strong>!</p>
            </div>
            {/if}
            <div class="warning">
                <p>You are using an experimental version of MagIRC.<br />
                <strong>DO NOT USE IN PRODUCTION!</strong><br />
                This version is not fully functional and most certainly does contain bugs</p>
            </div>
            <table width="100%" cellpadding="5" cellspacing="2">
                <tr>
                    <td align="center" valign="bottom"><a href="?page=registration"><img src="img/register.png" alt="Product Registration" width="32" height="32" /><br />Register</a></td>
                    <td align="center" valign="bottom"><a href="http://www.magirc.org/"><img src="img/homepage.png" alt="Project Homepage" width="32" height="32" /><br />Homepage</a></td>
                    <td align="center" valign="bottom"><a href="http://www.denorastats.org/support/"><img src="img/support.png" alt="Product Support" width="32" height="32" /><br />Support</a></td>
                    <td align="center" valign="bottom">
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="width:100px;">
                        <div>
                        <input type="hidden" name="cmd" value="_s-xclick" />
                        <input type="hidden" name="hosted_button_id" value="5270963" />
                        <input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" name="submit" alt="PayPal - The safer, easier way to pay online." />
                        <img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
                        </div>
                        </form>
                    </td>
                </tr>
            </table>
        </td>
        <td valign="top" style="text-align:center;">
            <img src="img/pie.png" alt="" />
            <table>
                <tr><th style="text-align:right;">MagIRC version</th><td style="text-align:left;">{$smarty.const.VERSION_FULL}</td></tr>
                <tr><th style="text-align:right;">IRCd protocol</th><td style="text-align:left;">{$config.ircd_type}</td></tr>
                <tr><th style="text-align:right;">PHP version</th><td style="text-align:left;">{$version.php}</td></tr>
                <tr><th style="text-align:right;">Smarty version</th><td style="text-align:left;">{$smarty.version}</td></tr>
                <tr><th style="text-align:right;">MySQL client version</th><td style="text-align:left;">{$version.sql_client}</td></tr>
            </table>
        </td>
    </tr>
</table>

{/block}