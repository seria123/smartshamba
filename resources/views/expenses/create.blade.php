                                     <input type="date" name="expense_date" class="form-input-modern" value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="description" class="form-input-modern" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Amount (KES) *</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">KES</span>
                                        <input type="number" step="0.01" name="amount" class="form-input-modern pl-10" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Expense Date *</label>
                                    <input type="date" name="expense_date" class="form-input-modern" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Expense Type *</label>
                                    <select name="expense_type" class="form-input-modern" required>
                                        <option value="">Select Type</option>
                                        <option value="inputs">Inputs</option>
                                        <option value="labor">Labor</option>
                                        <option value="equipment">Equipment</option>
                                        <option value="fertilizer">Fertilizer</option>
                                        <option value="seeds">Seeds</option>
                                        <option value="pesticides">Pesticides</option>
                                        <option value="fuel">Fuel</option>
                                        <option value="maintenance">Maintenance</option>
                                        <option value="transport">Transport</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-input-modern">
                                        <option value="crop_production">Crop Production</option>
                                        <option value="livestock">Livestock</option>
                                        <option value="operations">Operations</option>
                                        <option value="administrative">Administrative</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <input type="text" name="payment_method" class="form-input-modern">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Receipt Number</label>
                                    <input type="text" name="receipt_number" class="form-input-modern">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Worker</label>
                                    <select name="worker_id" class="form-input-modern">
                                        <option value="">Select Worker</option>
                                        @foreach(\App\Models\Worker::all() as $worker)
                                        <option value="{{ $worker->id }}">{{ $worker->fullName() }} - {{ $worker->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-textarea-modern" rows="2"></textarea>
                        </div>