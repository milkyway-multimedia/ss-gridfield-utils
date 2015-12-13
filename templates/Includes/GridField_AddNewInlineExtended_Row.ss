$EditableColumns

<tr class="ss-gridfield-inline-new-extended--row<% if $EditableColumns %>
     ss-gridfield-inline-new-extended--row--has-columns<% if $OpenByDefault %> ss-gridfield-inline-new-extended--row--has-columns_open<% end_if %><% end_if %>"
    data-inline-new-extended-row="$placeholder">

    <% if $PrevColumnsCount %>
        <td class="ss-gridfield-inline-new-extended--row--table--fieldsHolder_before ss-gridfield-editable-row--fieldsHolder_before"
            colspan="$PrevColumnsCount">
        </td>
    <% end_if %>

    <td class="ss-gridfield-inline-new-extended--fieldsHolder ss-gridfield-editable-row--fieldsHolder"
        colspan="$ColumnCountWithoutActions">
        <h6>New $Model ($placeholder)</h6>
        <fieldset class="ss-gridfield-inline-new-extended--fields ss-gridfield-editable-row--fields">
            <% loop $Form.Fields %>
                $FieldHolder
            <% end_loop %>
        </fieldset>
    </td>
    <td class="ss-gridfield-inline-new-extended--buttons col-buttons">
        <% if not $EditableColumns %>
            <button class="ss-gridfield-inline-new-extended--row-delete gridfield-button-delete ss-ui-button"
                    data-icon="cross-circle"></button>
        <% end_if %>
    </td>
</tr>
