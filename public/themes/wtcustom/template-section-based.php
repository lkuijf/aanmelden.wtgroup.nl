<?php
/*
Template Name: Section-based
*/
?>
<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
    <?php
    $sections = carbon_get_the_post_meta( 'crb_sections' );
    foreach ( $sections as $section ) {
        switch ( $section['_type'] ) {
            case 'text':
                ?>
                <div class="section-text">
                    <?php echo wpautop( $section['text'] ); ?>
                </div>
                <?php
                break;
            case 'file_list':
                ?>
                <div class="section-file-list">
                    <h2>Resources:</h2>
                    <ul>
                        <?php foreach ( $section['files'] as $file_item ) : ?>
                            <li>
                                <a href="<?php echo wp_get_attachment_url( $file_item['file'] ); ?>" target="_blank"><?php echo get_the_title( $file_item['file'] ); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php
                break;
            case 'related_posts':
                ?>
                <div class="section-related-posts">
                    <h2>Related posts:</h2>
                    <ul>
                        <?php foreach ( $section['posts'] as $post_item ) : ?>
                            <li>
                                <a href="<?php echo get_the_permalink( $post_item['id'] ); ?>"><?php echo get_the_title( $post_item['id'] ); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php
                break;
        }
    }
    ?>
<?php endwhile; ?>

<?php get_footer(); ?>