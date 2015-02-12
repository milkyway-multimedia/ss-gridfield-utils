<script type="text/x-tmpl" class="ss-gridfield-add-inline-extended--template">
	<tr class="ss-gridfield-inline-new-extended--row">
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
	</tr>
</script>
