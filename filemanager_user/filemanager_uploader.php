<?php 
if (!isset($core))
{
	require_once '../filemanager_user_core.php';
	$core = new filemanager_user_core();
    $core->userInfo();
    require_once '../filemanager_language_user.php';
}
if ($core->isLogin())
{
	if($core->user_can_do_it($core->user_id, "upload_folders", $core->user_limitation))
    {
        if(isset($_POST["upload_dir"]))
        {
            $_SESSION["lift_file_manager_list_of_files"] = array();
            $_POST["upload_dir"] .= '/';
            $allowedExts = $core->get_option("allow_uploads_".$core->user_id);
            $set_allowExt = implode(";", $allowedExts);
            $allowedExts = implode(", ", $allowedExts);
            $size = $core->get_option("user_upload_limit_".$core->user_id);
            if( preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT']) or strpos( $_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0' ) !== false )
            {
                // if IE
?>
            <script language="Javascript">
                function fileUpload(form, action_url, div_id) {
                    // Create the iframe...
                    var iframe = document.createElement("iframe");
                    iframe.setAttribute("id", "upload_iframe");
                    iframe.setAttribute("name", "upload_iframe");
                    iframe.setAttribute("width", "0");
                    iframe.setAttribute("height", "0");
                    iframe.setAttribute("border", "0");
                    iframe.setAttribute("style", "width: 0; height: 0; border: none;");

                    // Add to document...
                    form.parentNode.appendChild(iframe);
                    window.frames['upload_iframe'].name = "upload_iframe";

                    iframeId = document.getElementById("upload_iframe");

                    // Add event...
                    var eventHandler = function () {

                        if (iframeId.detachEvent) iframeId.detachEvent("onload", eventHandler);
                        else iframeId.removeEventListener("load", eventHandler, false);

                        // Message from server...
                        if (iframeId.contentDocument) {
                            content = iframeId.contentDocument.body.innerHTML;
                        } else if (iframeId.contentWindow) {
                            content = iframeId.contentWindow.document.body.innerHTML;
                        } else if (iframeId.document) {
                            content = iframeId.document.body.innerHTML;
                        }

                        document.getElementById(div_id).innerHTML = content;

                        // Del the iframe...
                        setTimeout('iframeId.parentNode.removeChild(iframeId)', 250);
                    }

                    if (iframeId.addEventListener) iframeId.addEventListener("load", eventHandler, true);
                    if (iframeId.attachEvent) iframeId.attachEvent("onload", eventHandler);

                    // Set properties of form...
                    form.setAttribute("target", "upload_iframe");
                    form.setAttribute("action", action_url);
                    form.setAttribute("method", "post");
                    form.setAttribute("enctype", "multipart/form-data");
                    form.setAttribute("encoding", "multipart/form-data");

                    // Submit the form...
                    form.submit();

                    document.getElementById(div_id).innerHTML = "<?php language_filter("Uploading...", false, true)?>";
                }
            </script>
            <div class="alert alert-info" style="text-align: center;"><?php language_filter("You can upload file with following extensions")?>: <br> <?php echo $allowedExts;?><br><b><?php language_filter("Maximum upload size")?>: </b><?php echo $size;?> MB</div>
            <div style="margin: 0 0 0 0">
                <form>
                    <div class="col-md-12">
                        <div class="col-md-9">
                            <input type="file" name="datafile[]" class="btn btn-default" multiple="multiple"/><input type="hidden" name="uploadDir" value="<?php echo $_POST["upload_dir"];?>">
                        </div>
                        <div class="col-md-3">
                            <input type="button" value="<?php language_filter("Upload")?>" onClick="fileUpload(this.form,'filemanager_user/upload.php','upload'); return false;" class="btn btn-default">
                        </div>
                    </div>
                    <br><br>
                    <div id="upload"></div>
                </form>
            </div>
<?php
            }
            else
            {
?>
            <div class="alert alert-info" style="text-align: center;"><?php language_filter("You can upload file with following extensions")?>: <br> <?php echo $allowedExts;?><br><b><?php language_filter("Maximum upload size")?>: </b><?php echo $size;?> MB</div>

            <!-- D&D Markup -->
            <div id="drag-and-drop-zone" class="uploader">
                <div><?php language_filter( "dragDropStr" );?></div>
                <div class="or"><?php language_filter( "or" );?></div>
                <div class="browser">
                    <label>
                        <span><?php language_filter( "add_files_btn" );?></span>
                        <input type="file" name="file[]" multiple="multiple" title='<?php echo language_filter( "add_files_btn", false, true );?>'>
                    </label>
                </div>
            </div>
            <!-- /D&D Markup -->

            <div id="fileList">

                <!-- Files will be places here -->

            </div>

            <script type="text/javascript">
                function add_file(id, file)
                {
                    var template = '' +
                            '<div class="file" id="uploadFile' + id + '">' +
                            '<div class="info">' +
                            '<span class="filename" title="Size: ' + file.size + 'bytes - Mimetype: ' + file.type + '">' + file.name + '</span><br /><small><?php language_filter( "Status" );?>: <span class="status"><?php language_filter( "Waiting" );?></span></small>' +
                            '</div>' +
                            '<div class="progress">' +
                            '<div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">' +
                            '<span class="sr-only">0% Complete</span>' +
                            '</div>' +
                            '</div>' +
                            '</div>';

                    $('#fileList').prepend(template);
                }

                function update_file_status(id, status, message)
                {
                    $('#uploadFile' + id).find('span.status').html(message).addClass(status);
                }

                function update_file_progress(id, percent)
                {
                    $('#uploadFile' + id).find('div.progress-bar').width(percent);
                }

                $('#drag-and-drop-zone').dmUploader({
                    url: 'filemanager_user/upload.php',
                    dataType: 'json',
                    allowedTypes: '*',
                    extFilter: '<?php echo $set_allowExt;?>',
                    extraData: {
                        'uploadDir': "<?php echo $_POST["upload_dir"];?>",
                        'extra_dir_show': "<?php echo (int) mysql_real_escape_string( $_POST["extra_dir_show"] );?>"
                    },
                    onInit: function() {

                    },
                    onBeforeUpload: function(id) {
                        update_file_status(id, 'uploading', "<?php language_filter( 'uploading', false, true );?>");
                    },
                    onNewFile: function(id, file) {
                        add_file(id, file);
                    },
                    onComplete: function() {
                        $.post(
                                "filemanager_user/upload.php",
                                {
                                    uploadedDirectory: "<?php echo $_POST["upload_dir"];?>",
                                    extra_dir_show: "<?php echo (int) mysql_real_escape_string( $_POST["extra_dir_show"] );?>"
                                },
                                function( data, status ) {
                                    // Nothing
                                }
                        );
                    },
                    onUploadProgress: function(id, percent) {
                        var percentStr = percent + '%';
                        update_file_progress(id, percentStr);
                    },
                    onUploadSuccess: function(id, data) {
                        update_file_status(id, data.status.toLowerCase(), data.msg );
                        update_file_progress(id, '100%');
                    },
                    onUploadError: function(id, message) {
                        update_file_status(id, 'Error', 'Server Error');
                    },
                    onFileTypeError: function(file) {

                    },
                    onFileSizeError: function(file) {

                    },
                    onFallbackMode: function(message) {
                        alert('Browser not supported(do something else here!): ' + message);
                    }
                });
            </script>
<?php
            }
        }
	}
}
?>