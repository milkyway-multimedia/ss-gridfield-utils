<div class="ss-gridfield--minorActions-holder ss-gridfield--minorActions-{$TargetFragmentID}-holder">
    <% if $Title %>
        <label class="ss-gridfield--minorActions-label ss-gridfield--minorActions-{$TargetFragmentID}-label">
            $Title
        </label>
    <% end_if %>
    <div class="ss-gridfield--minorActions ss-gridfield--minorActions-{$TargetFragmentID}">
        <div class="ss-gridfield--minorActions-dropdown ss-gridfield--minorActions-{$TargetFragmentID}-dropdown">
            <% if $ShowEmptyString %>
                <div class="ss-gridfield--minorActions-title ss-gridfield--minorActions-{$TargetFragmentID}-title">
                    $ShowEmptyString
                </div>
            <% end_if %>
            $Actions
        </div>
    </div>
</div>