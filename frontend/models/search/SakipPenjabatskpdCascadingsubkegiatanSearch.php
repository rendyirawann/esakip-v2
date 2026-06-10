<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipPenjabatskpdCascadingsubkegiatan;

/**
 * SakipPenjabatskpdCascadingsubkegiatanSearch represents the model behind the search form of `frontend\models\SakipPenjabatskpdCascadingsubkegiatan`.
 */
class SakipPenjabatskpdCascadingsubkegiatanSearch extends SakipPenjabatskpdCascadingsubkegiatan
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refpenjabatcascadingsubkegiatan_id', 'refpenjabatskpd_id', 'refeselon_id', 'refcascadingprogram_id', 'refcascadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refindikatorsubkegiatan_id', 'refskpd_id', 'refperiode_id', 'refsasaranrenstra_id', 'refindikatorsasaranrenstra_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id'], 'integer'],
            [['uraian_sasaransubkegiatan', 'uraian_indikatorsubkegiatan', 'subkegiatan_target', 'subkegiatan_satuan', 'target_rkt', 'target_rkt_p', 'target_pk', 'target_pk_p', 'realisasi', 'capaian', 'keterangan', 'analisis'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipPenjabatskpdCascadingsubkegiatan::find();

        // Eager load relations for performance
        $query->with(['refSubkegiatan', 'refKegiatan', 'refProgram', 'refPeriode']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'refpenjabatcascadingsubkegiatan_id' => $this->refpenjabatcascadingsubkegiatan_id,
            'refpenjabatskpd_id' => $this->refpenjabatskpd_id,
            'refeselon_id' => $this->refeselon_id,
            'refcascadingprogram_id' => $this->refcascadingprogram_id,
            'refcascadingkegiatan_id' => $this->refcascadingkegiatan_id,
            'refcascadingsubkegiatan_id' => $this->refcascadingsubkegiatan_id,
            'refindikatorsubkegiatan_id' => $this->refindikatorsubkegiatan_id,
            'refskpd_id' => $this->refskpd_id,
            'refperiode_id' => $this->refperiode_id,
            'refsasaranrenstra_id' => $this->refsasaranrenstra_id,
            'refindikatorsasaranrenstra_id' => $this->refindikatorsasaranrenstra_id,
            'refprogram_id' => $this->refprogram_id,
            'refkegiatan_id' => $this->refkegiatan_id,
            'refsubkegiatan_id' => $this->refsubkegiatan_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_sasaransubkegiatan', $this->uraian_sasaransubkegiatan])
            ->andFilterWhere(['like', 'uraian_indikatorsubkegiatan', $this->uraian_indikatorsubkegiatan])
            ->andFilterWhere(['like', 'subkegiatan_target', $this->subkegiatan_target])
            ->andFilterWhere(['like', 'subkegiatan_satuan', $this->subkegiatan_satuan])
            ->andFilterWhere(['like', 'target_rkt', $this->target_rkt])
            ->andFilterWhere(['like', 'target_rkt_p', $this->target_rkt_p])
            ->andFilterWhere(['like', 'target_pk', $this->target_pk])
            ->andFilterWhere(['like', 'target_pk_p', $this->target_pk_p])
            ->andFilterWhere(['like', 'realisasi', $this->realisasi])
            ->andFilterWhere(['like', 'capaian', $this->capaian])
            ->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'analisis', $this->analisis]);

        return $dataProvider;
    }
}
