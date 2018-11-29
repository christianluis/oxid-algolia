[{$smarty.block.parent}]

<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="ALGOLIA_ATTRIBUTE_SETTINGS_SEARCHABLE"}]
    </td>
    <td class="edittext">
        <input type="hidden" name="editval[oxattribute__algoliasearchable]" value='0' [{$readonly}]>
        <input class="edittext" type="checkbox" name="editval[oxattribute__algoliasearchable]" value='1' [{if $edit->oxattribute__algoliasearchable->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="ALGOLIA_ATTRIBUTE_SETTINGS_SEARCHABLE_HELP"}]
    </td>
</tr>
<tr>
    <td class="edittext" width="120">
        [{oxmultilang ident="ALGOLIA_ATTRIBUTE_SETTINGS_FILTERABLE"}]
    </td>
    <td class="edittext">
        <input type="hidden" name="editval[oxattribute__algoliafilterable]" value='0' [{$readonly}]>
        <input class="edittext" type="checkbox" name="editval[oxattribute__algoliafilterable]" value='1' [{if $edit->oxattribute__algoliafilterable->value == 1}]checked[{/if}] [{$readonly}]>
        [{oxinputhelp ident="ALGOLIA_ATTRIBUTE_SETTINGS_FILTERABLE_HELP"}]
    </td>
</tr>
