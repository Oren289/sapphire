export function cropRectangle(width, height, horizontal = 50, vertical = 50, zoom = 1) {
    const size = Math.min(width, height) / zoom;

    return { x: (width - size) * horizontal / 100, y: (height - size) * vertical / 100, size };
}

export default () => ({
    image: null,
    preview: null,
    horizontal: 50,
    vertical: 50,
    zoom: 1,
    error: '',
    busy: false,
    loading: false,
    selection: 0,

    async selectImage(event) {
        const selection = ++this.selection;
        this.image = null;
        this.preview = null;
        this.loading = false;
        this.error = '';
        this.horizontal = this.vertical = 50;
        this.zoom = 1;
        const file = event.target.files[0];
        if (! file) return;
        if (! ['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            this.error = 'Choose a JPG, PNG or WebP image up to 5 MB.';
            return;
        }
        this.loading = true;
        const url = URL.createObjectURL(file);
        try {
            const image = new Image();
            image.src = url;
            await image.decode();
            if (selection !== this.selection) return;
            if (image.naturalWidth > 8000 || image.naturalHeight > 8000) {
                throw new Error('Choose an image no larger than 8000 × 8000 pixels.');
            }
            this.image = image;
            this.renderPreview();
        } catch (error) {
            if (selection === this.selection) this.error = error.message || 'This image could not be opened.';
        } finally {
            URL.revokeObjectURL(url);
            if (selection === this.selection) this.loading = false;
        }
    },

    canvas(maximumSize) {
        const { x, y, size } = cropRectangle(this.image.naturalWidth, this.image.naturalHeight, this.horizontal, this.vertical, this.zoom);
        const canvas = document.createElement('canvas');
        canvas.width = canvas.height = Math.max(1, Math.min(maximumSize, Math.floor(size)));
        const context = canvas.getContext('2d');
        context.fillStyle = '#ffffff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(this.image, x, y, size, size, 0, 0, canvas.width, canvas.height);
        return canvas;
    },

    renderPreview() {
        if (this.image) this.preview = this.canvas(512).toDataURL('image/jpeg', 0.9);
    },

    async submit(event) {
        if (this.busy || this.loading || this.error) return;
        const form = event.target;
        this.busy = true;
        try {
            if (this.image) {
                const blob = await new Promise(resolve => this.canvas(2048).toBlob(resolve, 'image/jpeg', 0.92));
                if (! blob) throw new Error('The picture could not be cropped. Please try again.');
                const transfer = new DataTransfer();
                transfer.items.add(new File([blob], 'cropped-picture.jpg', { type: 'image/jpeg' }));
                form.elements.picture.files = transfer.files;
            }
            HTMLFormElement.prototype.submit.call(form);
        } catch (error) {
            this.error = error.message;
            this.busy = false;
        }
    },
});
