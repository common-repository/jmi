<?php
/*
Controller name: JMI
Controller description: Controller specific to Just Map It! extension
*/
class JSON_API_JMI_Controller {
	const max = 50;

    public function posts() {
        global $json_api;

        // See also: http://codex.wordpress.org/Template_Tags/query_posts
        $results = $json_api->introspector->get_posts(array('posts_per_page' => $this->getMaxParamter()));
        return $this->getPostsAndTags($results);
    }

    public function last_posts() {
        global $json_api;

        $id = $json_api->query->get('id');

        // Get the last ($max) posts
        $posts = $json_api->introspector->get_posts(
        array('posts_per_page' => $this->getMaxParamter(),
              'post__not_in' => array($id)
             )
        );

        // Get post by id
        if ($id) {
            $results = get_posts(array('p' => $id));
            if(!$results) {
                $json_api->error("Post not found.");
            }
            $post = new JSON_API_Post($results[0]);
            // Add the current post to the list
            $posts[] = $post;
        }


        return $this->getPostsAndTags($posts);
    }

    public function category_posts() {
        global $json_api;
        $category = $json_api->introspector->get_current_category();

        if (!$category) {
            $json_api->error("Not found.");
        }

        $results = $json_api->introspector->get_posts(
            array(
                'cat' => $category->id,
                'posts_per_page' => $this->getMaxParamter()
            )
        );
        return $this->getPostsAndTags($results);
    }

    public function tag_posts() {
        global $json_api;
        $tag = $json_api->introspector->get_current_tag();
        if (!$tag) {
            $json_api->error("Not found.");
        }


        $results = $json_api->introspector->get_posts(
            array(
                'tag' => $tag->slug,
                'posts_per_page' => $this->getMaxParamter()
			)
        );
        return $this->getPostsAndTags($results);
    }

    /**
     * Get posts that share the same tags as the given one.
     */
    public function related_posts() {
        global $json_api;
        $id = $json_api->query->get('id');

        if ($id) {
	    $results = get_posts(array('p' => $id));
            if(!$results) {
                $json_api->error("Post not found.");
            }
            $post = new JSON_API_Post($results[0]);
        }
        else {
            $json_api->error("Include 'id' var in your request.");
        }

        // Get all tags from the current post
        if(is_array($results) && count($results) > 0) {
            $res = $results[0]; // first element in the array is the result post
            if(is_array($res->tags) && count($res->tags) > 0) {
                $tags_id = array();
                foreach($res->tags as $tag) {
                  $tags_id[] = $tag->term_id;
                }
              

                // Prepare query arguments and search posts containing the same tags
                // as the current post
                $posts = $json_api->introspector->get_posts(
                    array('tag__in' => $tags_id,
                          'post__not_in' => array($id),
                          'posts_per_page' => $this->getMaxParamter())
                );
            }
        }

        if(!$posts) $posts = array();
	    // Adding the current post to the list
     	$posts[] = $post;

        return $this->getPostsAndTags($posts);
    }

	/**
	 * Get the max parameter from the query params
	 * Or return the default max value if not found
	 */
	protected function getMaxParamter() {
		global $json_api;
		$max = $json_api->query->get('max');
        return (!$max) ? JSON_API_JMI_Controller::max : $max;
	}

    /**
     * Extract posts and tags informations from the result provided.
     * <p>
     * Utility method to construct the result for the Just Map It! Server.
     * </p>
     *
     * @param result  a an object containing posts after a query on the WP db
     * @return        an array containg the posts and tags in a correct format
     *                the jmi REST connector.
     */
    protected function getPostsAndTags($result){
        $posts = array();
        $tags = array();

        foreach ($result as $post) {
            $tags_id = array();
            if(is_array($post->tags)) {
                foreach ($post->tags as $tag) {
                    if (!isset($tags[$tag->id])) {
                        $tags[$tag->id] = array(
                            'id'    => "$tag->id",
                            'slug'  => $tag->slug,
                            'url'   => get_tag_link($tag->id),
                            'title' => $tag->title,
                            'description' => $tag->description
                        );
                    }
                    $tags_id[] = array(
                        'id' => "$tag->id"
                    );
                }
            }
            $posts[] = array(
                'id' => "$post->id",
                'slug' => $post->slug,
                'url' => $post->url,
                'title' => $post->title,
                'tags' => $tags_id
            );
        }
        return array(
            'posts' => $posts,
            'tags' => array_values($tags)
        );
    }
}
?>
