import tailwindcss from '@tailwindcss/postcss';
import cssnano from 'cssnano';

export default {
	plugins: [tailwindcss(), cssnano()],
};
