import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';

registerBlockType( 'lmntl/unsplash-swiper', {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,
} );
