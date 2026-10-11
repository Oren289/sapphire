

import Alpine from 'alpinejs';
import elapsedTime from './elapsed-time';
import imagePreview from './image-preview';
import imageCrop from './image-crop';

window.Alpine = Alpine;

Alpine.data('elapsedTime', elapsedTime);
Alpine.data('imagePreview', imagePreview);
Alpine.data('imageCrop', imageCrop);

Alpine.start();
