"use strict";

var datatable;
// Class definition
var KTDatatablesServerSide = (function () {
    let dbTable = "orders";
    // if(typeof userId !== 'undefined'){
    //     dbTable +='?user_id='+userId;
    //     console.log(dbTable);
    // }
    // Private functions
    var initDatatable = function () {
        datatable = $("#kt_datatable_orders").DataTable({
            language: language,
            searchDelay: searchDelay,
            processing: processing,
            serverSide: serverSide,
            order: [],
            stateSave: saveState,
            select: {
                style: "multi",
                selector: 'td:first-child input[type="checkbox"]',
                className: "row-selected",
            },
            ajax: {
                url: `/dashboard/${dbTable}`,
            },
            columns: [
                { data: "id" },
                { data: "customer.full_name", name: "customer_id" },
                { data: "customer.phone", name: "customer_id" },
                { data: "status", name: "status" },

                { data: "created_at" },
                { data: null },
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    targets: 3, // status column
                    render: function (data, type, row, meta) {
                        let statusLabels = {
                            1: { text: "Pending", color: "warning" },
                            2: { text: "Approved", color: "success" },
                            3: { text: "Rejected", color: "danger" },
                        };

                        let status = statusLabels[row.status];

                        if (status) {
                            return `<span class="badge bg-${status.color}">${__(
                                status.text
                            )}</span>`;
                        } else {
                            return `<span class="badge bg-secondary">Unknown</span>`;
                        }
                    },
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                        <a href="/dashboard/orders/${
                            data.id
                        }" class="btn btn-light btn-active-light-primary btn-sm ">
                            <span class="indicator-label">
                                ${__("Show")}
                            </span>
                            <i class="fa-regular fa-eye fs-4"></i>
                        </a>
                        `;
                    },
                },
            ],
            // Add data-filter attribute
            createdRow: function (row, data, dataIndex) {
                // $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
            },
        });

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        datatable.on("draw", function () {
            //initToggleToolbar();
            // if(typeof userId === 'undefined'){
            // toggleToolbars();
            // }
            KTMenu.createInstances();
        });
    };

    // Public methods
    return {
        init: function () {
            initDatatable();
            // if(typeof userId === 'undefined'){
            handleSearchDatatable();
            handleFilterRowsByColumnIndex();
            //initToggleToolbar();
            // }
        },
    };
})();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
