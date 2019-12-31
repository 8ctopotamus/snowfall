const { all_snowfall_data } = wp_data

function renderDayCharts(numDays) {
  const canvas = document.getElementById(`most-snow-${numDays}-days`);
  canvas.height = (all_snowfall_data.length * 10);
  ctx = canvas.getContext('2d');

  const cityNames = all_snowfall_data.map(city => city['Name']);
  const dates = all_snowfall_data.map(city => city[`${numDays} days DATE`]);
  const qty = all_snowfall_data.map(city => city[`${numDays} days QTY`]);

  new Chart(ctx, {
    type: 'horizontalBar',
    data: {
      labels: cityNames,
      datasets: [{
        label: 'Inches',
        data: qty,
      }]
    },
    options: {
      tooltips: {
        callbacks: {
          footer: function(tooltipItem) {
              return dates[tooltipItem[0].index];
          }
        }
      }
    }
  });
}

[1, 2, 3].forEach(idx => renderDayCharts(idx));