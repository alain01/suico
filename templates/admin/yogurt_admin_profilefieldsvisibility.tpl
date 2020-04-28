<br>
<div class="head">
    <form id="<{$addform.name}>" method="<{$addform.method}>" action="<{$addform.action}>">
        <{foreach item=element from=$addform.elements}>
            <{$element.caption}> <{$element.body}>
        <{/foreach}>
    </form>
</div>

<table>
    <{foreach item=field from=$fields key=field_id}>
        <tr class="<{cycle values='odd,even'}>">
            <td class="width20"><{$field}></td>
            <td>
                <{if isset($visibilities.$field_id)}>
                    <ul>
                        <{foreach item=visibility from=$visibilities.$field_id}>
                            <{assign var=user_gid value=$visibility.user_group}>
                            <{assign var=profile_gid value=$visibility.profile_group}>
                            <li>
                                <{$smarty.const._AM_YOGURT_FIELDVISIBLEFOR}> <{$groups.$user_gid}>
                                <{$smarty.const._AM_YOGURT_FIELDVISIBLEON}> <{$groups.$profile_gid}>
                                <a href="profile_fieldsvisibility.php?op=del&amp;field_id=<{$field_id}>&amp;ug=<{$user_gid}>&amp;pg=<{$profile_gid}>"
                                   title="<{$smarty.const._DELETE}>">
                                    <img src="<{$xoops_url}>/modules/yogurt/assets/images/no.png" alt="<{$smarty.const._DELETE}>"/>
                                </a>
                            </li>
                        <{/foreach}>
                    </ul>
                <{else}>
                    <{$smarty.const._AM_YOGURT_FIELDNOTVISIBLE}>
                <{/if}>
            </td>
        </tr>
    <{/foreach}>
</table>