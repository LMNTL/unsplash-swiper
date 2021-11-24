import { __ } from '@wordpress/i18n';

import { useBlockProps, BlockControls, InspectorControls } from '@wordpress/block-editor';
import { RangeControl, TextControl, CheckboxControl, SelectControl, Dashicon } from '@wordpress/components'

import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation} from 'swiper';

import breedList from './breeds.json';

import './editor.scss';


export default function Edit({ attributes, setAttributes }) {
	const onChangeImageCount = ( val ) => {
		setAttributes({	imageCount: val	});
	}
	const onChangeQuery = ( val ) => {
		setAttributes( { query: val })
	};
	const onChangeCatMode = ( val ) => {
		setAttributes( { catMode: val });
	};
	const onChangeBreed = (val) => {
		console.log(val);
		setAttributes( { breed: val } )
	}

	return (
		<div { ...useBlockProps() }>
			<BlockControls>
				<RangeControl
					min={ 2 }
					max={ 10 }
					value={ attributes.imageCount }
					onChange={ onChangeImageCount }
				/>
			</BlockControls>

			<InspectorControls>
				<div id="csgwp-controls__inspector">
					<fieldset>
						<legend className="blocks-base-control__label">
							{ __( 'Images to load', 'unsplash-swiper' ) }
						</legend>
						<RangeControl
							min={ 2 }
							max={ 10 }
							value={ attributes.imageCount }
							onChange={ onChangeImageCount }
						/>
					</fieldset>
					{ attributes.catMode ? (
						<fieldset>
							<legend className="blocks-base-control__label">
								{ __( 'Breed', 'unsplash-swiper' ) }
							</legend>
							<SelectControl
								value={ attributes.breed }
								onChange={ onChangeBreed }
								options={ breedList }
							/>
						</fieldset>
					) : (
						<fieldset>
							<legend className="blocks-base-control__label">
								{ __( 'Search Query', 'unsplash-swiper' ) }
							</legend>
							<TextControl
								value={ attributes.query }
								onChange={ onChangeQuery }
							/>
						</fieldset>
					)}
					<fieldset>
						<legend className="blocks-base-control__label">
							{ __( 'Cat Mode ', 'unsplash-swiper' ) }
							<Dashicon icon="pets" />
						</legend>
						<CheckboxControl
							checked={ attributes.catMode }
							onChange={ onChangeCatMode }
						/>
					</fieldset>
				</div>
			</InspectorControls>
		    <Swiper
				className={"unsplash-csgwp__swiper"}
				modules={[Navigation]}
				loop={true}
				navigation
			>
				<SwiperSlide className={"unsplash-csgwp__slider swiper-no-swiping"}>Slide 1</SwiperSlide>
				<SwiperSlide className={"unsplash-csgwp__slider swiper-no-swiping"}>Slide 2</SwiperSlide>
				<SwiperSlide className={"unsplash-csgwp__slider swiper-no-swiping"}>Slide 3</SwiperSlide>
			</Swiper>
		</div>
	);
}
