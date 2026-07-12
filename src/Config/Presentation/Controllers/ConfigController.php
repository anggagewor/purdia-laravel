<?php

namespace Purdia\Config\Presentation\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Purdia\Config\Application\Actions\BulkSetConfigAction;
use Purdia\Config\Application\Actions\DeleteConfigAction;
use Purdia\Config\Application\Actions\SetConfigAction;
use Purdia\Config\Application\DTOs\SetConfigDTO;
use Purdia\Config\Domain\Contracts\ConfigRepository;
use Purdia\Config\Presentation\Requests\BulkSetConfigRequest;
use Purdia\Config\Presentation\Requests\SetConfigRequest;
use Purdia\Config\Presentation\Resources\V1\ConfigResource;
use Purdia\Shared\Support\ApiResponse;

class ConfigController extends Controller
{
    public function __construct(
        private readonly ConfigRepository $configs,
    ) {}

    /**
     * List all config groups.
     * GET /api/configs
     */
    public function index(): JsonResponse
    {
        $groups = $this->configs->getGroups();

        return ApiResponse::success($groups);
    }

    /**
     * Get all configs for a group.
     * GET /api/configs/{group}
     */
    public function show(string $group): JsonResponse
    {
        $configs = $this->configs->getGroup($group);

        return ApiResponse::success(ConfigResource::collection($configs));
    }

    /**
     * Set a single config value within a group.
     * PUT /api/configs/{group}
     */
    public function update(SetConfigRequest $request, string $group, SetConfigAction $action): JsonResponse
    {
        $dto = new SetConfigDTO(
            group: $group,
            key: $request->validated('key'),
            value: $request->validated('value'),
            type: $request->validated('type', 'string'),
        );

        $config = $action->execute($dto);

        return ApiResponse::success(new ConfigResource($config));
    }

    /**
     * Bulk set configs for a group.
     * PUT /api/configs/{group}/bulk
     */
    public function bulk(BulkSetConfigRequest $request, string $group, BulkSetConfigAction $action): JsonResponse
    {
        $items = collect($request->validated('configs'))->map(
            fn (array $item) => new SetConfigDTO(
                group: $group,
                key: $item['key'],
                value: $item['value'],
                type: $item['type'] ?? 'string',
            )
        )->all();

        $configs = $action->execute($items);

        return ApiResponse::success(ConfigResource::collection($configs));
    }

    /**
     * Delete a config entry.
     * DELETE /api/configs/{group}/{key}
     */
    public function destroy(string $group, string $key, DeleteConfigAction $action): JsonResponse
    {
        $action->execute($group, $key);

        return ApiResponse::success(message: 'Config deleted successfully.');
    }
}
