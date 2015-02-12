<tr class="ss-gridfield-editable-row--row" data-id="$ID" data-class="$ClassName">
    <% if $PrevColumnsCount %>
        <td class="ss-gridfield-editable-row--fieldsHolder_before" colspan="$PrevColumnsCount">
        </td>
    <% end_if %>
    <td class="ss-gridfield-editable-row--fieldsHolder" colspan="$OtherColumnsCount">
        <h6>Editing $Title</h6>
        <fieldset class="ss-gridfield-editable-row--fields">
            <% loop $Form.Fields %>
                $FieldHolder
            <% end_loop %>
        </fieldset>
    </td>
</tr>