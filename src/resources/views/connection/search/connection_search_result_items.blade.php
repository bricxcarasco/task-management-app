<search-results-section
    :image_path='@json(asset(config("bphero.profile_image_directory") . config("bphero.profile_image_filename")))'
    :request_data='@json($requestData)'>
</search-results-section>