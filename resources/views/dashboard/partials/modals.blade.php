<!-- Report Detail Modal -->
<div class="fixed inset-0 z-[1050] flex items-center justify-center p-4" id="reportDetailModal" aria-hidden="true"
    x-data="{ isVisible: false }" @open-modal.window="if($event.detail.id === 'reportDetailModal') isVisible = true"
    x-show="isVisible" x-cloak>
    <div class="fixed inset-0 bg-slate-800/70" @click="isVisible = false" x-show="isVisible"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
    <div class="relative bg-card-bg rounded-md shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col"
        @click.away="isVisible = false" x-show="isVisible" x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95">
        <div class="flex justify-between items-center p-6 border-b border-border-color flex-shrink-0">
            <h5 class="text-xl font-bold text-text-primary">Report Details</h5>
            <button type="button" class="text-2xl text-text-secondary hover:text-text-primary p-2 -m-2"
                @click="isVisible = false">×</button>
        </div>
        <div class="flex-grow overflow-hidden flex">
            <div class="grid grid-cols-1 md:grid-cols-2 w-full">
                <!-- Property Details Column -->
                <div class="p-6 overflow-y-auto border-e-0 md:border-e border-border-color">
                    <h6 class="text-[0.9rem] font-semibold text-text-secondary uppercase tracking-wider mb-4">Property
                        Related to the Report</h6>
                    <h4 class="text-xl font-bold text-text-primary mb-4" id="modal_property_location"></h4>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div class="flex items-center gap-2"><i
                                class="fas fa-money-bill-wave text-text-secondary w-5 text-center text-[0.9rem]"></i><span
                                class="font-semibold text-success text-base" id="modal_property_price"></span></div>
                        <div class="flex items-center gap-2"><i
                                class="fas fa-tag text-text-secondary w-5 text-center text-[0.9rem]"></i><span
                                class="font-medium text-text-primary text-[0.9rem]" id="modal_property_category"></span>
                        </div>
                        <div class="flex items-center gap-2"><i
                                class="fas fa-building text-text-secondary w-5 text-center text-[0.9rem]"></i><span
                                class="font-medium text-text-primary text-[0.9rem]" id="modal_property_type"></span>
                        </div>
                        <div class="flex items-center gap-2"><i
                                class="fas fa-ruler-combined text-text-secondary w-5 text-center text-[0.9rem]"></i><span
                                class="font-medium text-text-primary text-[0.9rem]" id="modal_property_size"></span>
                        </div>
                        <div class="flex items-center gap-2"><i
                                class="fas fa-bed text-text-secondary w-5 text-center text-[0.9rem]"></i><span
                                class="font-medium text-text-primary text-[0.9rem]" id="modal_property_rooms"></span>
                        </div>
                        <div class="flex items-center gap-2"><i
                                class="fas fa-layer-group text-text-secondary w-5 text-center text-[0.9rem]"></i><span
                                class="font-medium text-text-primary text-[0.9rem]" id="modal_property_floor"></span>
                        </div>
                    </div>
                    <h6 class="text-[0.9rem] font-semibold text-text-secondary uppercase tracking-wider mt-6 mb-4">
                        Property Description</h6>
                    <div class="mt-6 p-4 bg-page-bg border border-border-color rounded-lg text-text-secondary text-[0.9rem] leading-7 max-h-[150px] overflow-y-auto"
                        id="modal_property_content"></div>
                    <a href="#" id="modal_property_url" target="_blank"
                        class="block w-full mt-6 text-center py-3 px-6 bg-accent-primary-light text-accent-primary font-semibold rounded-lg hover:bg-indigo-200 transition-colors">Open
                        Property Page</a>
                </div>
                <!-- Report Actions Column -->
                <div class="p-6 overflow-y-auto">
                    <form id="updateReportStatusForm" class="flex flex-col h-full">
                        <input type="hidden" name="report_id" id="modal_report_id">
                        <div class="mb-6">
                            <label class="block font-semibold text-text-secondary mb-2">Report Subject</label>
                            <p id="modal_report_subject" class="text-xl font-bold text-text-primary"></p>
                        </div>
                        <div class="mb-6 flex-grow">
                            <label class="block font-semibold text-text-secondary mb-2">Report Description</label>
                            <div class="p-4 bg-page-bg border border-border-color rounded-lg text-text-secondary text-sm leading-7 min-h-[100px]"
                                id="modal_report_message"></div>
                        </div>
                        <div class="mb-6">
                            <label for="modal_report_status_select"
                                class="block font-semibold text-text-secondary mb-2">Take Action</label>
                            <select name="status" id="modal_report_status_select"
                                class="form-select w-full py-3 px-3 border-gray-300 rounded-lg shadow-sm focus:border-accent-primary focus:ring-accent-primary text-base">
                                <option value="pending">Pending</option>
                                <option value="resolved">Resolved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full py-3 px-4 bg-accent-primary text-white font-semibold rounded-lg hover:bg-indigo-700 disabled:opacity-70 disabled:cursor-not-allowed transition-colors"
                            id="saveReportStatusBtn">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
