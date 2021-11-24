import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';

registerBlockType( 'create-block/plugin-test', {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,
} );
