import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/ui/glass_container.dart';
import '../../core/providers/api_providers.dart';

class OrdersScreen extends ConsumerWidget {
  const OrdersScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final ordersAsyncValue = ref.watch(ordersProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Siparişler', style: TextStyle(fontWeight: FontWeight.bold)),
      ),
      body: ordersAsyncValue.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (error, stack) => Center(child: Text('Hata: $error')),
        data: (orders) {
          if (orders.isEmpty) {
            return const Center(child: Text('Henüz sipariş yok.'));
          }
          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: orders.length,
            itemBuilder: (context, index) {
              final order = orders[index];
              final isPending = order['status'] == 'processing' || order['status'] == 'pending';
              return Card(
                margin: const EdgeInsets.only(bottom: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                child: ListTile(
                  contentPadding: const EdgeInsets.all(16),
                  leading: Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isPending ? Colors.orange.withOpacity(0.1) : Colors.green.withOpacity(0.1),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(
                      isPending ? Icons.pending_actions : Icons.check_circle_outline,
                      color: isPending ? Colors.orange : Colors.green,
                    ),
                  ),
                  title: Text('Sipariş #${order['id']}', style: const TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: Text('${order['customer_name']}\n${order['total']}'),
                  isThreeLine: true,
                  trailing: Chip(
                    label: Text(isPending ? 'Bekliyor' : 'Tamamlandı'),
                    backgroundColor: isPending ? Colors.orange.withOpacity(0.1) : Colors.green.withOpacity(0.1),
                    labelStyle: TextStyle(
                      color: isPending ? Colors.orange : Colors.green,
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  onTap: () {
                    // TODO: Sipariş Detayı
                  },
                ),
              );
            },
          );
        },
      ),
    );
  }
}
