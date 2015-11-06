$SearchForm

<h3><% _t("RESULTS", "Results") %></h3>

<div class="Actions add-existing-picker-actions<% if $isAsync %> hide<% end_if %>">

    <a href="$Link('add')" class="action ss-ui-action-constructive icon-accept ss-ui-button ui-button ui-widget ui-state-default ui-corner-all add-existing-picker-actions--add-items" role="button" aria-disabled="false"><% _t("GridField_AddExistingPicker.ADD_SELECTED_ITEMS", "Add Selected Items") %></a>

</div>

<div class="add-existing-search-results">
	<% if $Items %>
		<ul class="add-existing-search-items add-existing-picker-items" data-add-link="$Link('add')" data-undo-link="$Link('undo')">
			<% loop $Items %>
				<li class="add-existing-picker-item $EvenOdd"><a href="#" data-id="$ID" class="add-existing-picker-item--link">$Title</a></li>
			<% end_loop %>
		</ul>
	<% else %>
		<p><% _t("NOITEMS", "There are no items.") %></p>
	<% end_if %>

	<% if $Items.MoreThanOnePage %>
		<ul class="add-existing-search-pagination">
			<% if $Items.NotFirstPage %>
				<li><a href="$Items.PrevLink">&laquo;</a></li>
			<% end_if %>

			<% loop $Items.PaginationSummary(4) %>
				<% if $CurrentBool %>
					<li class="current">$PageNum</li>
				<% else_if $Link %>
					<li><a href="$Link">$PageNum</a></li>
				<% else %>
					<li>&hellip;</li>
				<% end_if %>
			<% end_loop %>

			<% if $Items.NotLastPage %>
				<li><a href="$Items.NextLink">&raquo;</a></li>
			<%end_if %>
		</ul>
	<% end_if %>
</div>
