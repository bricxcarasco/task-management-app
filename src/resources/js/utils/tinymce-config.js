// TinyMCE reussable config file

// API url for upload
const baseUrl = `/api/knowledges/tiny-cme`;
const uploadUrl = `${baseUrl}/upload-file`;

const TinyMCEConfig = {
  selector: 'textarea#content',
  language: 'ja',
  height: 400,
  automatic_uploads: false,
  paste_data_images: true,
  images_upload_url: uploadUrl,
  autosave_ask_before_unload: false,
  branding: false,
  plugins: [
    'preview',
    ',importcss',
    ',searchreplace',
    'paste',
    'autolink',
    'autosave',
    'save',
    'directionality',
    'code',
    'visualblocks',
    'visualchars',
    'image',
    'link',
    'media',
    'template',
    'codesample',
    'table',
    'charmap',
    'pagebreak',
    'nonbreaking',
    'anchor',
    'insertdatetime',
    'advlist',
    'lists',
    'wordcount',
    'help',
    'charmap',
    'quickbars',
    'emoticons',
    'print',
    'hr',
  ],
  toolbar:
    'undo redo |' +
    'bold italic underline strikethrough blockquote |' +
    'fontfamily fontsize blocks |' +
    'alignleft aligncenter alignright alignjustify |' +
    'outdent indent |' +
    'numlist bullist |' +
    'forecolor backcolor removeformat |' +
    'pagebreak |' +
    'charmap emoticons |' +
    'preview save print |' +
    'insertfile image media template link anchor codesample |' +
    'ltr rtl | hr',
};

export default TinyMCEConfig;
