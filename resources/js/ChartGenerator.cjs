const { ChartJSNodeCanvas } = require('chartjs-node-canvas');
console.log('Loading ChartGenerator');

try {
    class ChartGenerator {
        static generateChart(labels, data) {
            console.log('Starting chart generation with labels:', labels, 'data:', data);
            const width = 400;
            const height = 200;
            console.log('Creating ChartJSNodeCanvas instance');
            const chart = new ChartJSNodeCanvas({ width, height });
            console.log('Chart instance created');

            const configuration = {
                type: 'bar',
                data: { labels, datasets: [{ data, backgroundColor: 'rgba(0, 177, 79, 0.6)' }] },
                options: { scales: { y: { beginAtZero: true } } }
            };
            console.log('Configuration set');

            const image = chart.renderToBufferSync(configuration);
            console.log('Chart generated');
            return image.toString('base64');
        }
    }
    module.exports = ChartGenerator;
} catch (error) {
    console.error('Error in ChartGenerator:', error.message);
}