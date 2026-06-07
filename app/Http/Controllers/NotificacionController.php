<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $title = '¿Eliminar notificación?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);

        return view('notificaciones.index');
    }

    private function dataTableResponse(Request $request)
    {
        $user = Auth::user();

        $query = $user->notifications()->select('notifications.*');

        $totalRecords = $query->count();

        $filteredRecords = $totalRecords;

        $data = $query->orderBy('created_at', 'desc')
            ->skip($request->get('start', 0))
            ->take($request->get('length', 10))
            ->get();

        $dataFormatted = [];
        foreach ($data as $row) {
            $readIcon = $row->read_at
                ? '<i class="bi bi-envelope-open text-muted"></i>'
                : '<i class="bi bi-envelope-fill text-primary"></i>';

            $title = $row->data['title'] ?? '';
            $body = $row->data['body'] ?? '';

            $btnMarkRead = '';
            if (!$row->read_at) {
                $btnMarkRead = '<button type="button" data-id="'.$row->id.'" class="btn-mark-read btn btn-xs btn-square btn-neutral" title="Marcar como leída"><i class="bi bi-eye"></i></button>';
            }

            $btnDelete = '<a href="'.route('notificaciones.destroy', $row->id).'" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';

            $acciones = '<div class="hstack gap-2 justify-content-end">'.$btnMarkRead.$btnDelete.'</div>';

            $dataFormatted[] = [
                $readIcon,
                '<div>'.$title.'</div><small class="text-muted">'.$body.'</small>',
                '<small>'.$row->created_at->format('d/m/Y h:i A').'</small><br><small class="text-secondary">'.$row->created_at->diffForHumans().'</small>',
                $acciones,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }

    public function unread()
    {
        $user = Auth::user();

        $total = $user->unreadNotifications->count();

        $ultimas = $user->unreadNotifications()
            ->take(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '',
                    'body' => $n->data['body'] ?? '',
                    'action_url' => $n->data['action_url'] ?? '#',
                    'created_at_diff' => $n->created_at->diffForHumans(),
                    'read_at' => $n->read_at,
                ];
            });

        return response()->json([
            'total' => $total,
            'ultimas' => $ultimas,
        ]);
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::where('notifiable_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notificaciones.index');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('notificaciones.index');
    }

    public function destroy($id)
    {
        $notification = DatabaseNotification::where('notifiable_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        alert()->success('Notificación eliminada exitosamente.');
        return redirect()->route('notificaciones.index');
    }
}
