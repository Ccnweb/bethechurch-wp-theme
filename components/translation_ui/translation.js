let wp_posts = {};
let leaf_nodes;

function toggle_translation() {
    let curr_el = jQuery(this);
    if (leaf_nodes && leaf_nodes.eq(0).attr('contenteditable')) {
        close_translation();
        curr_el.css({background: 'black'});
        curr_el.html('<i class="fas fa-edit"></i>');
    } else {
        curr_el.html('You can now start editing text in this page');
        curr_el.css({background: 'green'});
        init_translation();
    }
}

function init_translation() {
    console.log('Activating translation mode...');
    let activation_ongoing = false;

    leaf_nodes = get_leaf_nodes();
    
    // select leaf nodes : all elements which don't have child nodes
    leaf_nodes.click(async function(e) {
        if (jQuery(this).attr('btc-editable') == 'false' || activation_ongoing) return;
        e.preventDefault();
        e.stopPropagation();
        let curr_el = jQuery(this);
        console.log('click el', curr_el.text() == curr_el.html(), jQuery(this).attr('not-editable'))
        
        let original_text = curr_el.text();
        
        if (!curr_el.attr('parent-post') || !wp_posts[curr_el.attr('parent-post')]) {
            activation_ongoing = true;
            // find parent post
            let parent_post_id = curr_el.closest('[id^="post__"]');
            if (parent_post_id.length < 1) return console.error("Could not find parent post");

            // find parent post slug
            let parent_slug = /^post__(.+)$/.exec(parent_post_id.attr('id'));
            if (parent_slug.length < 2) return console.error('Could not find parent post slug ', parent_slug);
            parent_slug = parent_slug[1];
            console.log('Parent post : ',parent_slug);

            if (!wp_posts[parent_slug]) {
                // retrieve the parent post content
                let parent_post = await get_post_by_slug(parent_slug);
                if (parent_post.length < 1) return console.error('Could not retrieve parent post content', parent_post);
                parent_post = parent_post[0];
                wp_posts[parent_slug] = parent_post;
                console.log("Parent post json : ", parent_post);
            }

            curr_el.attr('parent-post', parent_slug);
            curr_el.attr('contenteditable', true);
            
            activation_ongoing = false; // we release the translation init lock
        }

        let updating_post = {}
        curr_el.on('keypress', async function(e) {
            if(e.which != 13) return;
            e.preventDefault();
            e.stopPropagation();
            let parent_post = wp_posts[curr_el.attr('parent-post')];
            if (updating_post[parent_post.id]) return console.log("Please wait, post is already updating");
            console.log('updating post...', parent_post);
            updating_post[parent_post.id] = true;

            let new_text = curr_el.text();
            if (new_text == original_text) return console.log('nothing to update for the post '+parent_post.id);

            // check if we have to update the title
            let matches = new RegExp(original_text, "g").exec(parent_post.title.rendered);
            console.log('title matches', matches);
            if (matches && matches.length) {
                let new_post_title = parent_post.title.rendered.replace(original_text, new_text);
                console.log('updating post '+parent_post.id+' with new title : ', new_post_title);
                let b = await update_post(parent_post.id, {title: new_post_title});
                original_text = new_text;
                updating_post[parent_post.id] = false;
            
            // check if we have to update the content
            } else {
                let matches = new RegExp(original_text, "g").exec(parent_post.content.rendered);
                console.log('content matches', matches);
                if (matches && matches.length) {
                    let new_post_content = parent_post.content.rendered.replace(original_text, new_text);
                    console.log('updating post '+parent_post.id+' with new content : ', new_post_content);
                    let b = await update_post(parent_post.id, {content: new_post_content});
                    original_text = new_text;
                    updating_post[parent_post.id] = false;
                } else {
                    curr_el.text(original_text);
                    return console.error('could not find matching text in parent post title and content', original_text, parent_post);
                }
            }

            
        })

        // check in parent post title if there is original_text
        // test : update the post title
        /* let new_title = 'FESTIVALL DES PAROISSES';
        jQuery.ajax({
            method: 'POST',
            url: '/wp-json/wp/v2/posts/'+parent_post.id,
            dataType: "json",
            data: {
                _wpnonce: translationAjaxData.nonce,
                'title': new_title,
            },
            success: (data) => {console.log('success save', data)},
            error: (data) => {console.log('error save', data)},
        }); */
    });

    console.log('%c Translation mode activated', 'color:green');
}

function get_leaf_nodes() {
    let leaf_nodes = jQuery(':not(:has(*))')
                    .add('[btc-editable="true"]')
                    .not('[btc-editable="false"]')
                    .not(':empty');
    console.log('leaf_nodes : ', typeof leaf_nodes, leaf_nodes.length);
    return leaf_nodes;
}

function close_translation() {
    leaf_nodes.attr('contenteditable', false);
    leaf_nodes.off('click');
}

async function get_post_by_slug(slug) {
    /**
     * Retrieves the 
     * 
     */

    return fetch('/wp-json/wp/v2/posts?slug='+slug).then(response => response.json());
}

function update_post(id, data) {
    data._wpnonce = translationAjaxData.nonce;
    return new Promise((resolve, reject) => {
        jQuery.ajax({
            method: 'POST',
            url: '/wp-json/wp/v2/posts/'+id,
            dataType: "json",
            data,
            success: (data) => {
                wp_posts[data.slug] = data;
                console.log('success saving post', data);
                resolve(data);
            },
            error: (data) => {
                console.log('error saving post', data);
                reject(data);
            },
        });
    })
}

function html_to_btc_markdown(html_str) {
    return html_str.replace(/\<br\>/g, '§');
}