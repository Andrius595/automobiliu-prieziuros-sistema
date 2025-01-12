import type {Appointment} from "~/types/Appointment";

const getOrCreateTooltip = (chart) => {
    let tooltipEl = chart.canvas.parentNode.querySelector('div');

    if (!tooltipEl) {
        tooltipEl = document.createElement('div');
        tooltipEl.style.background = 'rgba(0, 0, 0, 0.7)';
        tooltipEl.style.borderRadius = '3px';
        tooltipEl.style.color = 'white';
        tooltipEl.style.opacity = 1;
        tooltipEl.style.pointerEvents = 'none';
        tooltipEl.style.position = 'absolute';
        tooltipEl.style.transform = 'translate(-50%, 0)';
        tooltipEl.style.transition = 'all .1s ease';

        const table = document.createElement('table');
        table.style.margin = '0px';

        tooltipEl.appendChild(table);
        chart.canvas.parentNode.appendChild(tooltipEl);
    }

    return tooltipEl;
};

export const externalTooltipHandler = (context) => {
    // Tooltip Element
    const {chart, tooltip} = context;
    const tooltipEl = getOrCreateTooltip(chart);

    // Hide if no tooltip
    if (tooltip.opacity === 0) {
        tooltipEl.style.opacity = 0;
        return;
    }

    // Set Text
    if (tooltip.body) {
        const titleLines = tooltip.title || [];
        const bodyLines = tooltip.body.map(b => b.lines);

        const tableHead = document.createElement('thead');

        titleLines.forEach(title => {
            const tr = document.createElement('tr');
            tr.style.borderWidth = 0;

            const th = document.createElement('th');
            th.style.borderWidth = 0;
            const text = document.createTextNode(title);

            th.appendChild(text);
            tr.appendChild(th);
            tableHead.appendChild(tr);
        });

        const tableBody = document.createElement('tbody');
        bodyLines.forEach((body, i) => {
            const colors = tooltip.labelColors[i];
            const dataPoint = tooltip.dataPoints[i];
            const appointment = dataPoint.raw as Appointment;


            const span = document.createElement('span');
            span.style.background = colors.backgroundColor;
            span.style.borderColor = colors.borderColor;
            span.style.borderWidth = '2px';
            span.style.marginRight = '10px';
            span.style.height = '10px';
            span.style.width = '10px';
            span.style.display = 'inline-block';

            const tr = document.createElement('tr');
            tr.style.backgroundColor = 'inherit';
            tr.style.borderWidth = 0;

            const td = document.createElement('td');
            td.style.borderWidth = 0;

            const breakElement = document.createElement('br');
            const mileageSpan = document.createElement('span');
            mileageSpan.innerText = `Kilometražas: ${appointment.current_mileage}` + (appointment.mileage_type ? 'M' : 'KM');
            td.appendChild(mileageSpan);
            td.appendChild(breakElement.cloneNode());

            const serviceSpan = document.createElement('span');
            serviceSpan.innerText = `Servisas: ${appointment.service?.title}`;
            td.appendChild(serviceSpan);
            td.appendChild(breakElement.cloneNode());

            const recordSpan = document.createElement('span');
            recordSpan.innerText = `Įrašai: `;
            td.appendChild(recordSpan);
            td.appendChild(breakElement.cloneNode());
            appointment.records.forEach(record => {
                const recordSpan = document.createElement('span');
                recordSpan.innerText = record.short_description;
                td.appendChild(recordSpan);
                td.appendChild(breakElement.cloneNode());
            })

            tr.appendChild(td);
            tableBody.appendChild(tr);
        });

        const tableRoot = tooltipEl.querySelector('table');

        // Remove old children
        while (tableRoot.firstChild) {
            tableRoot.firstChild.remove();
        }

        // Add new children
        tableRoot.appendChild(tableHead);
        tableRoot.appendChild(tableBody);
    }

    const {offsetLeft: positionX, offsetTop: positionY} = chart.canvas;

    // Calculate position of tooltip relative to chart's canvas
    const tooltipX = positionX + tooltip.caretX;

// Determine if tooltip is in first half or second half horizontally
    const isTooltipInFirstHalf = tooltipX < (document.body.clientWidth / 2);

    console.log(positionX, tooltip.caretX, document.body.clientWidth / 2, isTooltipInFirstHalf)
// Calculate position adjustment for X axis (similar to previous logic)


// Calculate position adjustment for Y axis (similar to previous logic)
    let adjustedY = positionY + tooltip.caretY;
    const tooltipWidth = tooltipEl.offsetWidth;
    const tooltipHeight = tooltipEl.offsetHeight;
    const documentWidth = document.body.clientWidth;
    const windowHeight = window.innerHeight;

    if (adjustedY + tooltipHeight > windowHeight) {
        adjustedY = positionY + tooltip.caretY - tooltipHeight;
    }

// Adjust X position if tooltip goes beyond right or left edge
    let adjustedX;
    if (!isTooltipInFirstHalf) {
        adjustedX = positionX + tooltip.caretX - tooltipWidth/3;
    } else {
        adjustedX = positionX + tooltip.caretX + tooltipWidth/3;
    }

// Display tooltip
    tooltipEl.style.opacity = 1;
    tooltipEl.style.left = adjustedX + 'px';
    tooltipEl.style.top = adjustedY + 'px';
    tooltipEl.style.font = tooltip.options.bodyFont.string;
    tooltipEl.style.padding = tooltip.options.padding + 'px ' + tooltip.options.padding + 'px';

};