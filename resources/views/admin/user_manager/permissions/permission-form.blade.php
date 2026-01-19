    <table class="table table-bordered table-striped align-middle">

        <thead class="table-light">
            <tr class="text-center bg-light">
                <th style="width: 60px;">
                    <input type="checkbox" id="masterSelectAll">
                </th>
                <th class="text-start">Module</th>

                <th>
                    <input type="checkbox" class="columnSelect" data-column="allow">
                    <br><small class="text-muted">Allow</small>
                </th>

                <th>
                    <input type="checkbox" class="columnSelect" data-column="create">
                    <br><small class="text-muted">Create</small>
                </th>

                <th>
                    <input type="checkbox" class="columnSelect" data-column="edit">
                    <br><small class="text-muted">Edit</small>
                </th>

                <th>
                    <input type="checkbox" class="columnSelect" data-column="delete">
                    <br><small class="text-muted">Delete</small>
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach ($permissions as $group => $items)
                @php
                    $allow = $items->firstWhere('name', $group . '.allow');
                    $create = $items->firstWhere('name', $group . '.create');
                    $edit = $items->firstWhere('name', $group . '.edit');
                    $delete = $items->firstWhere('name', $group . '.delete');
                @endphp

                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="rowSelect">
                    </td>

                    <td class="fw-semibold text-primary">
                        <i class="ri-folder-2-line me-1 text-secondary"></i>
                        {{ ucfirst($group) }}
                    </td>

                    <td class="text-center">
                        @if ($allow)
                            <input type="checkbox" name="permissions[]" value="{{ $allow->name }}"
                                class="permission-checkbox checkbox-allow"
                                {{ in_array($allow->name, $userPermissions) ? 'checked' : '' }}>
                        @endif
                    </td>

                    <td class="text-center">
                        @if ($create)
                            <input type="checkbox" name="permissions[]" value="{{ $create->name }}"
                                class="permission-checkbox checkbox-create"
                                {{ in_array($create->name, $userPermissions) ? 'checked' : '' }}>
                        @endif
                    </td>

                    <td class="text-center">
                        @if ($edit)
                            <input type="checkbox" name="permissions[]" value="{{ $edit->name }}"
                                class="permission-checkbox checkbox-edit"
                                {{ in_array($edit->name, $userPermissions) ? 'checked' : '' }}>
                        @endif
                    </td>

                    <td class="text-center">
                        @if ($delete)
                            <input type="checkbox" name="permissions[]" value="{{ $delete->name }}"
                                class="permission-checkbox checkbox-delete"
                                {{ in_array($delete->name, $userPermissions) ? 'checked' : '' }}>
                        @endif
                    </td>

                </tr>
            @endforeach
        </tbody>

    </table>
