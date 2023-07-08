<div class="form-group ">
    <label class="control-label">{{ $name }} @if(@$field->required)*@endif</label>
    <span class="hint">{{ @$field->hint }}</span>
    <textarea name="message|{{ $name }}" class="form-control" rows="10"></textarea>
</div>
<script>launchTinymce()</script>
