<script type="text/x-tmpl" class="ss-gridfield-add-inline-extended--template">
	<tr class="ss-gridfield-inline-new-extended--row<% if $EditableColumns %> ss-gridfield-inline-new-extended--row--has-columns<% end_if %>">
	    <% if $EditableColumns %>
            <table class="ss-gridfield-inline-new-extended--row--table">
                $EditableColumns
                <tr class="ss-gridfield-inline-new-extended--row--table--form">
                    <% if $PrevColumnsCount %>
                    <td class="ss-gridfield-inline-new-extended--row--table--fieldsHolder_before" colspan="$PrevColumnsCount">
                    </td>
                     <% end_if %>
        <% end_if %>
	    <td class="ss-gridfield-inline-new-extended--fieldsHolder" colspan="$ColumnCountWithoutActions">
	    <h6>New $Model ({%=o.num%})</h6>
		    <fieldset class="ss-gridfield-inline-new-extended--fields">
                <% loop $Form.Fields %>
    $FieldHolder
<% end_loop %>
            </fieldset>
        </td>
        <td class="ss-gridfield-inline-new-extended--buttons col-buttons">
			<button class="ss-gridfield-inline-new-extended--row-delete gridfield-button-delete ss-ui-button" data-icon="cross-circle"></button>
		</td>

		 <% if $EditableColumns %>
         </tr>
         </table>
         <% end_if %>
	</tr>
</script>
